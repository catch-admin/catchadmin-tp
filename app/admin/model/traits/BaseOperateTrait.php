<?php

declare(strict_types=1);

namespace app\admin\model\traits;

use app\admin\exceptions\FailedException;
use app\admin\model\CatchModel;
use app\admin\support\enums\Status;
use Closure;
use think\Collection;
use think\contract\Arrayable;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;
use think\facade\Request;
use think\Model;

/**
 * base operate
 */
trait BaseOperateTrait
{
    /**
     * @param array $fields
     * @return mixed
     */
    public function getList(array $fields = ['*']): mixed
    {
        $builder = $this->field($fields)
            ->creator()
            ->quickSearch();

        // 数据权限
        if ($this->dataRange) {
            $builder = $builder->dataRange();
        }

        // before list
        if ($this->beforeGetList instanceof Closure) {
            $builder = call_user_func($this->beforeGetList, $builder);
        }

        // 排序
        if ($this->sortField && in_array($this->sortField, $this->getField())) {
            $builder = $builder->orderBy($this->aliasField($this->sortField), $this->sortDesc ? 'desc' : 'asc');
        }

        // 动态排序
        $dynamicSortField = Request::get('sortField');
        if ($dynamicSortField && $dynamicSortField <> $this->sortField) {
            $builder = $builder->orderBy($this->aliasField($dynamicSortField), Request::get('order', 'asc'));
        }
        $builder = $builder->orderByDesc($this->aliasField($this->getPk()));

        // 分页
        if ($this->isPaginate) {
            return $builder->paginate(Request::get('limit', $this->perPage, 'int'));
        }

        $data = $builder->select();
        // if set as tree, it will show tree data
        if ($this->asTree) {
            return $data->toTree();
        }

        return $data;
    }


    /**
     * save
     *
     * @param array $data
     * @return mixed
     */
    public function storeBy(array $data): mixed
    {
        // 保留原始 fields，防止里面的原始 field 被 filter 去除
        $originFields  = array_keys($data);

        $saveData = $this->filterData($data);
        foreach ($saveData as $field => $value) {
            $this->{$field} = $value;
        }

        if ($this->save()) {
            // 如果自动写入不为空
            if (!empty($this->autoWriteRelations)) {
                foreach ($this->autoWriteRelations as $relation) {
                    if (in_array($relation, $originFields) && count($data[$relation])) {
                        $this->relation($relation)->save($data[$relation]);
                    }
                }
            }

            return $this->getKey();
        }

        return false;
    }

    /**
     * create
     *
     * @param array $data
     * @return false|mixed
     */
    public function createBy(array $data): mixed
    {
        // 保留原始 fields，防止里面的原始 field 被 filter 去除
        $originFields  = array_keys($data);

        $model = $this->newInstance();

        $saveData = $this->filterData($data);
        if ($model->data($saveData)->save()) {
            // 如果自动写入不为空
            if (!empty($this->autoWriteRelations)) {
                foreach ($this->autoWriteRelations as $relation) {
                    if (in_array($relation, $originFields) && count($data[$relation])) {
                        $this->{$relation}()->attach($data[$relation]);
                    }
                }
            }
            return $model->getKey();
        }

        return false;
    }

    /**
     * update
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function updateBy($id, array $data): mixed
    {
        // 保留原始 fields，防止里面的原始 field 被 filter 去除
        $originFields  = array_keys($data);

        $model = $this->where($this->getPk(), $id)->find();

        $saveData = $this->filterData($data);
        if ($model->data($saveData)->save()) {
            // 如果自动写入不为空
            if (!empty($this->autoWriteRelations)) {
                foreach ($this->autoWriteRelations as $relation) {
                    if (in_array($relation, $originFields)) {
                        $model->{$relation}()->detach();
                        if (count($data[$relation])) {
                            $model->{$relation}()->attach($data[$relation]);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * filter data/ remove null && empty string
     *
     * @param array $data
     * @return array
     */
    protected function filterData(array $data): array
    {
        // 表单保存的数据集合
        $fillable = $this->getField();

        foreach ($data as $k => $val) {
            if ($this->autoNull2EmptyString && is_null($val)) {
                $data[$k] = '';
            }

            if (!empty($fillable) && !in_array($k, $fillable)) {
                unset($data[$k]);
            }

            if (in_array($k, [$this->getUpdatedAtColumn(), $this->getCreatedAtColumn()])) {
                unset($data[$k]);
            }
        }

        if (in_array($this->getCreatorIdColumn(), $this->getField())) {
            $data[$this->getCreatorIdColumn()] = request()->admin->id;
        }

        return $data;
    }


    /**
     * get first by ID
     *
     * @param $value
     * @param null $field
     * @param string[] $columns
     * @return ?Model
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function firstBy($value, $field = null, array $columns = ['*']): ?Model
    {
        $field = $field ?: $this->getPk();

        /* @var CatchModel|Model $model */
        $model = $this->where($field, $value)->field($columns)->find();

        if (!$model) {
            throw new FailedException('数据不存在');
        }

       if (!empty($this->autoWriteRelations)) {
           foreach ($this->autoWriteRelations as $relation) {
             //  $model->setAttr($relation, $model->{$relation}()->select());
           }
       }

        if ($this->afterFirstBy) {
            $model = call_user_func($this->afterFirstBy, $model);
        }

        return $model;
    }

    /**
     * delete model
     *
     * @param $id
     * @param bool $force
     * @return bool|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function deleteBy($id, bool $force = false): ?bool
    {
        /* @var Model $model */
        $model = $this->where($this->getPk(), $id)->find();

        if (in_array($this->getParentIdColumn(), $this->getField())
            && $this->where($this->getParentIdColumn(), $model->id)->find()
        ) {
            throw new FailedException('请先删除子级');
        }

        if ($force) {
            $deleted = $model->force()->delete();
        } else {
            $deleted = $model->delete();
        }

        if ($deleted) {
            if (!empty($this->autoWriteRelations)) {
                foreach ($this->autoWriteRelations as $relation) {
                    $model->{$relation}()->detach();
                }
            }
        }

        return $deleted;
    }

    /**
     * 批量删除
     *
     * @param array|string $ids
     * @param bool $force
     * @param Closure|null $callback
     * @return true
     */
    public function deletesBy(array|string $ids, bool $force = false, Closure $callback = null): bool
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $this->transaction(function () use ($ids, $force, $callback) {
            foreach ($ids as $id) {
                $this->deleteBy($id, $force);
            }

            if ($callback) {
                $callback($ids);
            }
        });

        return true;
    }

    /**
     * disable or enable
     *
     * @param $id
     * @param string $field
     * @return bool
     */
    public function toggleBy($id, string $field = 'status'): bool
    {
        $model = $this->firstBy($id);

        $status = $model->getData($field) == Status::ENABLE ? Status::DISABLE : Status::ENABLE;

        $model->data([$field => $status]);

        if ($model->save() && in_array($this->getParentIdColumn(), $this->getField())) {
            $this->updateChildren($id, $field, $model->getData($field));
        }

        return true;
    }

    /**
     *
     * @param array|string $ids
     * @param string $field
     * @return true
     */
    public function togglesBy(array|string $ids, string $field = 'status'): bool
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $this->transaction(function () use ($ids, $field) {
            foreach ($ids as $id) {
                $this->toggleBy($id, $field);
            }
        });

        return true;
    }


    /**
     * 递归处理
     *
     * @param int|array $parentId
     * @param string $field
     * @param int $value
     */
    public function updateChildren(mixed $parentId, string $field, mixed $value): void
    {
        if (!$parentId) {
            $parentId = [$parentId];
        }

        $childrenId = $this->whereIn($this->getParentIdColumn(), $parentId)->column('id');

        if (count($childrenId)) {
            if ($this->whereIn($this->getParentIdColumn(), $parentId)->update([
                $field => $value,
            ])) {
                $this->updateChildren($childrenId, $field, $value);
            }
        }
    }

    /**
     * alias field
     *
     * @param string|array $fields
     * @return string|array
     */
    public function aliasField(string|array $fields): string|array
    {
        $table = $this->getTable();

        if (is_string($fields)) {
            return sprintf('%s.%s', $table, $fields);
        }

        foreach ($fields as &$field) {
            $field = sprintf('%s.%s', $table, $field);
        }

        return $fields;
    }


    /**
     * get updated at column
     *
     * @return string|null
     */
    public function getUpdatedAtColumn(): ?string
    {
        $updatedAtColumn = $this->updateTime;

        if (!in_array($updatedAtColumn, $this->getField())) {
            $updatedAtColumn = null;
        }

        return $updatedAtColumn;
    }

    /**
     * get created at column
     *
     * @return string|null
     */
    public function getCreatedAtColumn(): ?string
    {
        $createdAtColumn = $this->createTime;

        if (!in_array($createdAtColumn, $this->getField())) {
            $createdAtColumn = null;
        }

        return $createdAtColumn;
    }

    /**
     *
     * @return string
     */
    public function getCreatorIdColumn(): string
    {
        return 'creator_id';
    }

    /**
     *
     * @return $this
     */
    protected function setCreatorId(): static
    {
        $this->data([$this->getCreatorIdColumn() => request()->admin->id]);

        return $this;
    }

    /**
     *
     * @param string $parentId
     * @return $this
     */
    public function setParentIdColumn(string $parentId): static
    {
        $this->parentIdColumn = $parentId;

        return $this;
    }

    /**
     *
     * @param string $sortField
     * @return $this
     */
    protected function setSortField(string $sortField): static
    {
        $this->sortField = $sortField;

        return $this;
    }

    /**
     *
     * @return $this
     */
    protected function setPaginate(bool $isPaginate = true): static
    {
        $this->isPaginate = $isPaginate;

        return $this;
    }


    /**
     * @return array
     */
    public function getField(): array
    {
        return $this->field;
    }

    /**
     * get parent id
     *
     * @return string
     */
    public function getParentIdColumn(): string
    {
        return $this->parentIdColumn;
    }

    /**
     * set data range
     *
     * @param bool $use
     * @return $this
     */
    public function setDataRange(bool $use = true): static
    {
        $this->dataRange = $use;

        return $this;
    }

    /**
     * @param bool $auto
     * @return $this
     */
    public function setAutoNull2EmptyString(bool $auto = true): static
    {
        $this->autoNull2EmptyString = $auto;

        return $this;
    }
}

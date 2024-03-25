<?php

declare(strict_types=1);

namespace app\admin\support;

use app\admin\model\CatchModel;
use think\db\Query;
use think\helper\Str;
use think\Paginator;

class CatchQuery extends Query
{
    /**
     * @param $model
     * @param string $joinField
     * @param string $currentJoinField
     * @param array $field
     * @param string $type
     * @param array $bind
     * @return $this
     */
    public function catchJoin($model, string $joinField, string $currentJoinField, array $field = [], string $type = 'INNER', array $bind = []): static
    {
        $tableAlias = null;

        if (is_string($model)) {
            $table = app($model)->getTable();
        } else {
            list($model, $tableAlias) = $model;
            $table = app($model)->getTable();
        }

        // 合并字段
        $this->options['field'] = array_merge($this->options['field'] ?? [], array_map(function ($value) use ($table, $tableAlias) {
            return ($tableAlias ?: $table) . '.' . $value;
        }, $field));

        return $this->join($tableAlias ? sprintf('%s %s', $table, $tableAlias) : $table

            , sprintf('%s.%s=%s.%s', $tableAlias ? $tableAlias : $table, $joinField, $this->getAlias(), $currentJoinField), $type, $bind);
    }

    /**
     * @param $model
     * @param string $joinField
     * @param string $currentJoinField
     * @param array $field
     * @param array $bind
     * @return $this
     */
    public function catchLeftJoin($model, string $joinField, string $currentJoinField, array $field = [], array $bind = []): static
    {
        return $this->catchJoin($model, $joinField, $currentJoinField, $field, 'LEFT', $bind);
    }

    /**
     * @param $model
     * @param string $joinField
     * @param string $currentJoinField
     * @param array $field
     * @param array $bind
     * @return $this
     */
    public function catchRightJoin($model, string $joinField, string $currentJoinField, array $field = [], array $bind = []): static
    {
        return $this->catchJoin($model, $joinField, $currentJoinField, $field, 'RIGHT', $bind);
    }

    /**
     * @param $field
     * @param bool $needAlias
     * @return $this|CatchQuery
     */
    public function withoutField($field, bool $needAlias = false): CatchQuery|static
    {
        if (empty($field)) {
            return $this;
        }

        if (is_string($field)) {
            $field = array_map('trim', explode(',', $field));
        }

        // 过滤软删除字段
        $field[] = $this->model->getDeleteAtField();

        // 字段排除
        $fields = $this->getTableFields();
        $field = $fields ? array_diff($fields, $field) : $field;

        if (isset($this->options['field'])) {
            $field = array_merge((array)$this->options['field'], $field);
        }

        $this->options['field'] = array_unique($field);

        if ($needAlias) {
            $alias = $this->getAlias();
            $this->options['field'] = array_map(function ($field) use ($alias) {
                return $alias . '.' . $field;
            }, $this->options['field']);
        }

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function catchSearch(array $params = []): static
    {
        $params = empty($params) ? \request()->param() : $params;

        if (empty($params)) {
            return $this;
        }

        foreach ($params as $field => $value) {
            $method = 'search' . Str::studly($field) . 'Attr';
            // value in [null, '']
            if ($value !== null && $value !== '' && method_exists($this->model, $method)) {
                $this->model->$method($this, $value, $params);
            }
        }

        return $this;
    }

    /**
     * @param string $field
     * @return CatchQuery
     */
    public function orderByDesc(string $field): static
    {
        return $this->order($field, 'desc');
    }

    /**
     * @param array $params
     * @return Query
     */
    public function quickSearch(array $params = []): Query
    {
        $params = array_merge(request()->all(), $params);

        if (! property_exists($this->model, 'searchable')) {
            return $this;
        }

        // filter null & empty string
        $params = array_filter($params, function ($value) {
            return (is_string($value) && strlen($value)) || is_numeric($value);
        });

        $wheres = [];

        if (! empty($this->model->searchable)) {
            foreach ($this->model->searchable as $field => $op) {
                // 临时变量
                $_field = $field;
                // contains alias
                if (str_contains($field, '.')) {
                    [, $_field] = explode('.', $field);
                }

                if (isset($params[$_field]) && $searchValue = $params[$_field]) {
                    $operate = strtolower($op);
                    $value = $searchValue;
                    if ($operate == 'op') {
                        $value = implode(',', $searchValue);
                    }

                    if ($operate == 'like') {
                        $value = "%{$searchValue}%";
                    }

                    if ($operate == 'rlike') {
                        $value = $searchValue. '%';
                    }

                    if ($operate == 'llike') {
                        $value = '%' .$searchValue;
                    }

                    if (str_ends_with('_at', $_field) || str_ends_with('_time', $_field)) {
                        $value = is_string($searchValue) ? strtotime($searchValue) : $searchValue;
                    }

                    $wheres[] = [$field, strtolower($op), $value];
                }
            }
        }

        // 组装 where 条件
        foreach ($wheres as $w) {
            [$field, $op, $value] = $w;
            if ($op == 'in') {
                // in 操作的值必须是数组，所以 value 必须更改成 array
                $this->whereIn($field, is_array($value) ? $value : explode(',', $value));
            } else {
                $this->where($field, $op, $value);
            }
        }

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getAlias()
    {
        return isset($this->options['alias']) ? $this->options['alias'][$this->getTable()] : $this->getTable();
    }

    /**
     * @param string $field
     * @param $condition
     * @param string $logic
     * @param string $option
     * @return Query
     */
    public function whereLike(string $field, $condition, string $logic = 'AND', string $option = 'both'): Query
    {
        switch ($option) {
            case 'both':
                $condition = '%' . $condition . '%';
                break;
            case 'left':
                $condition = '%' . $condition;
                break;
            default:
                $condition .= '%';
        }

        if (strpos($field, '.') === false) {
            $field = $this->getAlias() . '.' . $field;
        }

        return parent::whereLike($field, $condition, $logic);
    }

    /**
     * @param string $field
     * @param $condition
     * @param string $logic
     * @return $this
     */
    public function whereLeftLike(string $field, $condition, string $logic = 'AND'): Query
    {
        return $this->where($field, $condition, $logic, 'left');
    }

    /**
     * @param string $field
     * @param $condition
     * @param string $logic
     * @return $this
     */
    public function whereRightLike(string $field, $condition, string $logic = 'AND'): Query
    {
        return $this->where($field, $condition, $logic, 'right');
    }

    /**
     * @param $fields
     * @return $this
     */
    public function addFields($fields): static
    {
        if (is_string($fields)) {
            $this->options['field'][] = $fields;

            return $this;
        }

        $this->options['field'] = array_merge($this->options['field'], $fields);

        return $this;
    }

    public function paginate($listRows = null, $simple = false): Paginator
    {
        if (!$listRows) {
            $limit = \request()->param('limit');

            $listRows = $limit ?: CatchModel::LIMIT;
        }

        return parent::paginate($listRows, $simple); // TODO: Change the autogenerated stub
    }


    /**
     * @param string $order
     * @return $this
     */
    public function catchOrder(string $order = 'desc'): static
    {
        if (in_array('sort', array_keys($this->getFields()))) {
            $this->order($this->getTable() . '.sort', $order);
        }

        if (in_array('weight', array_keys($this->getFields()))) {
            $this->order($this->getTable() . '.weight', $order);
        }

        $this->order($this->getTable() . '.' . $this->getPk(), $order);

        return $this;
    }

    /**
     * @param callable $callable
     * @param string $as
     * @return $this
     */
    public function addSelectSub(callable $callable, string $as): static
    {
        $this->field(sprintf('%s as %s', $callable()->buildSql(), $as));

        return $this;
    }

    /**
     * @param $field
     * @param int $amount
     * @return int
     * @throws \think\db\exception\DbException
     */
    public function increment($field, int $amount = 1): int
    {
        return $this->inc($field, $amount)->update();
    }

    /**
     * @param $field
     * @param int $amount
     * @return int
     * @throws \think\db\exception\DbException
     */
    public function decrement($field, int $amount = 1): int
    {
        return $this->dec($field, $amount)->update();
    }
}

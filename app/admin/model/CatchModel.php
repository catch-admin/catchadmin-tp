<?php
namespace app\admin\model;

use app\admin\model\traits\ScopeTrait;
use app\admin\model\traits\BaseOperateTrait;
use app\admin\model\traits\WithAttributes;
use app\admin\model\traits\WithEvents;
use think\Model;
use think\model\concern\SoftDelete;
use app\admin\model\traits\RewriteTrait;

class CatchModel extends Model
{
    use SoftDelete, ScopeTrait, BaseOperateTrait, WithAttributes, WithEvents, RewriteTrait;

    protected int $perPage = 10;

    protected $createTime = 'created_at';

    protected bool $isSoftDelete = true;

    protected $updateTime = 'updated_at';

    protected string $deleteTime = 'deleted_at';
    protected $defaultSoftDelete = 0;

    protected $autoWriteTimestamp = true;

    public function __construct(array|object $data = [])
    {
        parent::__construct($data);

        // 隐藏字段
        $this->hidden = array_merge($this->hidden, $this->defaultHiddenFields());

        // 数据权限自动
        $this->autoDataRange();

        // 初始化
        $this->initialize();
    }


    /**
     * 子类实现自定义
     *
     * @return void
     */
    protected function initialize()
    {

    }

    /**
     * @return array
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * @return void
     */
    protected function autoDataRange(): void
    {
        // auto use data range
        foreach (class_uses_recursive(static::class) as $trait) {
            if (str_contains($trait, 'DataRangScopeTrait')) {
                $this->setDataRange();
            }
        }
    }

    /**
     * 重写通过属性控制，支持软删除
     *
     * @param bool $read
     * @return bool|string
     */
    public function getDeleteTimeField(bool $read = false): bool|string
    {
        if (!$this->isSoftDelete) {
            return false;
        }

        $field = property_exists($this, 'deleteTime') && isset($this->deleteTime) ? $this->deleteTime : 'delete_time';

        if (false === $field) {
            return false;
        }

        if (!str_contains($field, '.')) {
            $field = '__TABLE__.' . $field;
        }

        if (!$read && str_contains($field, '.')) {
            $array = explode('.', $field);
            $field = array_pop($array);
        }

        return $field;
    }
}

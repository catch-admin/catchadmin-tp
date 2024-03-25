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

    protected ?string $deleteTime = null;

    protected bool $isSoftDelete = true;

    protected $updateTime = 'updated_at';

    protected $defaultSoftDelete = 0;

    protected $autoWriteTimestamp = true;

    public function __construct(array|object $data = [])
    {
        parent::__construct($data);

        $this->useSoftDelete();

        // 隐藏字段
        $this->hidden = array_merge($this->hidden, $this->defaultHiddenFields());

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
    protected function useSoftDelete(): void
    {
        if ($this->isSoftDelete) {
            $this->deleteTime = 'deleted_at';
        }
    }
}

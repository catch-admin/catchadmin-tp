<?php
namespace app\admin\model;

use app\admin\controller\traits\ScopeTrait;
use app\admin\model\traits\BaseOperateTrait;
use app\admin\model\traits\WithAttributes;
use app\admin\model\traits\WithEvents;
use think\Model;
use think\model\concern\SoftDelete;

class CatchModel extends Model
{
    use SoftDelete, ScopeTrait, BaseOperateTrait, WithAttributes, WithEvents;

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
     * @return void
     */
    protected function useSoftDelete(): void
    {
        if ($this->isSoftDelete) {
            $this->deleteTime = 'delete_at';
        }
    }
}

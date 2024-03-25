<?php
namespace app\admin\controller\permissions;

use app\admin\controller\CatchController;
use app\admin\model\Admin as AdminModel;
use think\response\Json;

class Admin extends CatchController
{
    public function initialize(): void
    {
        $this->model = new AdminModel();
    }


    public function online():Json
    {
        return $this->success($this->user()->permissions());
    }
}

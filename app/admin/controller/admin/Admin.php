<?php
namespace app\admin\controller\admin;

use app\admin\controller\CatchController;
use think\response\Json;
use app\admin\model\Admin as AdminModel;

class Admin extends CatchController
{
    public function initialize(): void
    {
        $this->model = new AdminModel();
    }


    public function online():Json
    {
        return $this->success($this->user());
    }
}

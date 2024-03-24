<?php
namespace app\admin\controller\admin;

use app\admin\controller\CatchController;
use think\response\Json;

class Admin extends CatchController
{
    public function online():Json
    {
        return $this->success($this->user());
    }
}

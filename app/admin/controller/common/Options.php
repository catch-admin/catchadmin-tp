<?php
namespace app\admin\controller\common;

use app\admin\controller\CatchController;
use app\admin\support\options\Factory;

class Options extends CatchController
{
    public function get($option)
    {
        return $this->success(Factory::make($option)->get());
    }
}

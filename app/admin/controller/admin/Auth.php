<?php
namespace app\admin\controller\admin;

use app\admin\controller\CatchController;
use app\admin\exceptions\FailedException;
use app\Request;

class Auth extends CatchController
{
    public function attempt(Request $request)
    {
       return $this->success();
    }
}

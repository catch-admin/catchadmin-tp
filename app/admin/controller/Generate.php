<?php
namespace app\admin\controller;

use app\admin\support\generate\Generator;
use think\facade\Request;

class Generate extends CatchController
{
    public function index()
    {
        $params = Request::all();

        $generator = new Generator($params['controller'], $params['model'], $params['table'], $params);
        return $this->success($generator->generate());
    }
}

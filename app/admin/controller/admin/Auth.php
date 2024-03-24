<?php
namespace app\admin\controller\admin;

use app\admin\controller\CatchController;
use app\Request;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

class Auth extends CatchController
{
    /**
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function login(Request $request):Json
    {
        return $this->success([
            'token' => $this->auth()->attempt($request->all())
        ]);
    }


    /**
     * 退出
     *
     * @return Json
     */
    public function logout(): Json
    {
        return $this->success($this->auth()->logout());
    }
}

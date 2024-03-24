<?php
namespace app\admin\support\controller;

use app\admin\support\CatchAuth;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

trait Auth
{
    protected ?CatchAuth $auth = null;


    /**
     * @return CatchAuth
     */
    protected function auth(): CatchAuth
    {
        if (! $this->auth) {
            $this->auth = new CatchAuth();
        }

        return $this->auth;
    }


    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function user(): mixed
    {
        return $this->auth()->user();
    }


    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function uid(): mixed
    {
        return $this->user()['id'];
    }
}

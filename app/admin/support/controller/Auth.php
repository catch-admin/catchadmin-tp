<?php

namespace app\admin\support\controller;

trait Auth
{
    /**
     * @return mixed
     */
    protected function user(): mixed
    {
        return $this->request->admin;
    }


    /**
     * @return mixed
     */
    protected function uid(): mixed
    {
        return $this->user()['id'];
    }
}

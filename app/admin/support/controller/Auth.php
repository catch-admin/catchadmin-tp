<?php

namespace app\admin\support\controller;

use app\admin\model\Admin;

trait Auth
{
    /**
     *
     * @return Admin|null
     */
    protected function user(): ?Admin
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

<?php

namespace app\admin;

use think\Service;

class CatchService extends Service
{
    public function register(): void
    {
        // 服务注册
        $this->commands([
            \app\admin\commands\Install::class
        ]);
    }

    public function boot()
    {
        // 服务启动
    }
}

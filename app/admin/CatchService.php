<?php

namespace app\admin;

use think\Service;

class CatchService extends Service
{
    public function register(): void
    {
        // 注册 command
        $this->commands([
            \app\admin\commands\Install::class
        ]);


        // 注册后台路由
        $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . 'route' . DIRECTORY_SEPARATOR . 'admin.php');
    }

    public function boot()
    {
        // 服务启动
    }
}

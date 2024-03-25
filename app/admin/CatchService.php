<?php

namespace app\admin;

use app\admin\commands\Controller;
use app\admin\commands\Curd;
use app\admin\commands\Model;
use app\admin\support\CatchQuery;
use think\Service;
use app\admin\commands\Install;

class CatchService extends Service
{
    public function register(): void
    {
        // 注册 command
        $this->commands([
            Install::class,
            Controller::class,
            Model::class,
            Curd::class
        ]);


        // 注册后台路由
        $this->loadRoutesFrom(__DIR__ . DIRECTORY_SEPARATOR . 'route' . DIRECTORY_SEPARATOR . 'admin.php');

        // 注册 Query，支持扩展
        $this->registerQuery();
    }

    public function boot()
    {
        // 服务启动
    }

    protected function registerQuery(): void
    {
        $connections = $this->app->config->get('database.connections');

        // 支持多数据库配置注入 Query
        foreach ($connections as &$connection) {
            $connection['query'] = CatchQuery::class;
        }

        $this->app->config->set([
            'connections' => $connections
        ], 'database');
    }
}

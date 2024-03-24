<?php
namespace app\admin\controller;

use app\admin\support\controller\Auth;
use app\admin\support\controller\Resource;
use app\admin\support\controller\Response;

abstract class CatchController
{
    // 响应 trait
    // 资源路由操作 trait
    // 认证 trait
    use Response, Resource, Auth;
}

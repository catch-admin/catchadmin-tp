<?php
namespace app;

// 应用请求对象类
use app\admin\model\Admin;

class Request extends \think\Request
{
    // 设置后台 admin
    public ?Admin $admin = null;
}

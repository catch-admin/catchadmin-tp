<?php
namespace app\admin\support;

use app\admin\exceptions\FailedException;
use think\exception\RouteNotFoundException;
use think\Request;
use Throwable;
class CatchException
{
    public static function render(Request $request, Throwable $e): Throwable|FailedException
    {
        if ($request->header('Request-From') == 'Dashboard') {

            $message = $e->getMessage();

            if ($e instanceof RouteNotFoundException) {
                $message = $request->url() . ' 路由没有找到，请通过 [php think route:list] 查看路由是否存在或者对应的请求方法是否正确';
            }

            return new FailedException($message);
        }

        return $e;
    }
}

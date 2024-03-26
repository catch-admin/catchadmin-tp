<?php
namespace app\admin\middleware;

use app\admin\support\log\Operate;
use think\Middleware;
use think\Request;

class JsonResponseMiddleware extends Middleware
{
    public function handle(Request $request, \Closure $next)
    {
        $server = $request->server();
        $server['HTTP_ACCEPT'] = 'application/json';
        $request->withServer($server);

        return $next($request);
    }

    public function end($response): void
    {
        app(Operate::class)->handle(app('request'), $response);
    }
}

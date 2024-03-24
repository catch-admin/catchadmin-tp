<?php
namespace app\admin\middleware;

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
}

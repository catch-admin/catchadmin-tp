<?php
declare(strict_types=1);

namespace app\admin\middleware;

use Closure;
use think\Request;
use think\Response;

/**
 * 跨域请求支持
 */
class AllowCrossDomainMiddleware
{

    /**
     * 允许跨域请求
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response->header(\config('catch.cross_headers'));
    }
}

<?php
namespace app\admin\middleware;

use app\admin\exceptions\PermissionForbiddenException;
use app\admin\model\Admin;
use app\Request;

class PermissionGateMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->isGet()) {
           /// return $next($request);
        }

        /* @var Admin $admin */
        if (! $request->admin->can()) {
            throw new PermissionForbiddenException();
        }

        return $next($request);
    }
}

<?php

use app\admin\middleware\AuthMiddleware;
use app\admin\middleware\JsonResponseMiddleware;
use think\facade\Route;
use app\admin\controller\Auth;
use app\admin\middleware\PermissionGateMiddleware;

// 域名路由组
Route::group('api', function () {
    // 无需要认证的路由组
    Route::group( function () {
        Route::post('login', Auth::class . '/login');
        Route::post('logout', Auth::class . '/logout');
    });

    // 需要认证的路由组
    Route::group( function () {
        include __DIR__ . '/auth.php';
    })->middleware([
        AuthMiddleware::class,
        PermissionGateMiddleware::class
    ]);
})
->middleware([JsonResponseMiddleware::class]);

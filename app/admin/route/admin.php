<?php
use think\facade\Route;
use app\admin\middleware\JsonResponseMiddleware;

// 域名路由组
Route::group('api', function () {
    // 无需要认证的路由组
    Route::group( function () {
        Route::post('login', \app\admin\controller\admin\Auth::class . '@attempt');
    });

    // 需要认证的路由组
    Route::group( function () {
        // next
    });
})
->middleware(JsonResponseMiddleware::class);

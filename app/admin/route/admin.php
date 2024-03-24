<?php
use think\facade\Route;
use app\admin\middleware\JsonResponseMiddleware;
use app\admin\controller\admin\Auth;
use app\admin\controller\admin\Admin;

// 域名路由组
Route::group('api', function () {
    // 无需要认证的路由组
    Route::group( function () {
        Route::post('login', Auth::class . '/attempt');
    });

    // 需要认证的路由组
    Route::group( function () {
        Route::resource('admin', Admin::class)->except(['create', 'edit']);
        // next
    });
})
->middleware(JsonResponseMiddleware::class);

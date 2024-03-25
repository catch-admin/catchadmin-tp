<?php
use think\facade\Route;
use app\admin\middleware\JsonResponseMiddleware;
use app\admin\controller\admin\Auth;
use app\admin\controller\admin\Admin;
use app\admin\middleware\AuthMiddleware;

// 域名路由组
Route::group('api', function () {
    // 无需要认证的路由组
    Route::group( function () {
        Route::post('login', Auth::class . '/login');
        Route::post('logout', Auth::class . '/logout');
    });

    // 需要认证的路由组
    Route::group( function () {
        Route::resource('users', Admin::class)->except(['create', 'edit']);
        Route::get('user/online', Admin::class . '/online');
        // next
    })->middleware([
        AuthMiddleware::class
    ]);
})
->middleware([JsonResponseMiddleware::class]);

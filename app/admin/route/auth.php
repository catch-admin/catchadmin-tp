<?php

use app\admin\controller\permissions\Permissions;
use app\admin\controller\permissions\Departments;
use app\admin\controller\permissions\Roles;
use app\admin\controller\permissions\Jobs;
use think\facade\Route;
use app\admin\controller\permissions\Admin;
Route::resource('users', Admin::class)->except(['create', 'edit']);
Route::get('user/online', Admin::class . '/online');
Route::resource('jobs', Jobs::Class)->except(['create', 'edit']);
Route::resource('roles', Roles::Class)->except(['create', 'edit']);
Route::resource('departments', Departments::Class)->except(['create', 'edit']);
Route::resource('permissions', Permissions::Class)->except(['create', 'edit']);
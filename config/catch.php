<?php

return [
    // 前端项目目录
    'web_path' => root_path('web'),

    // 跨域头信息规则
    'cross_headers' => [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Headers' => 'Request-From,X-Requested-With, Content-Type, Token, X-Token, Authorization, Accept, Origin, X-CSRF-TOKEN',
        'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age' => 3600
    ],

    // 超级管理员 ID 集合
    'super_admin' => [1]
];

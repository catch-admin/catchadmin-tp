<?php
namespace app\admin\controller;

use think\response\Json;

abstract class CatchController
{
    public function success(mixed $data, string $message = 'success', int $code = 10000): Json
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }


    public function error(int $code, mixed $data = [], string $message = 'error'): Json
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}

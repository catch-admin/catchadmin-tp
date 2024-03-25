<?php
namespace app\admin\support\controller;

use app\admin\support\enums\Code;
use think\Paginator;
use think\response\Json;

/**
 * 响应 Trait
 */
trait Response
{
    public function success(mixed $data = [], string $message = 'success', int $code = Code::SUCCESS): Json
    {
        return json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function error(string $message = '', int $code = Code::FAILED): Json
    {
        return json([
            'code' => $code,
            'message' => $message,
        ]);
    }

    public function paginate(Paginator $list): Json
    {
        return json([
            'code'    => Code::SUCCESS,
            'message' => 'success',
            'total'   => $list->total(),
            'limit'   => $list->listRows(),
            'data'    => $list->getCollection(),
        ]);
    }
}

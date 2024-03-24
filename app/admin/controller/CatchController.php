<?php
namespace app\admin\controller;

use app\admin\support\enums\Code;
use think\Paginator;
use think\response\Json;

abstract class CatchController
{
    public function success(mixed $data, string $message = 'success', int $code = Code::SUCCESS): Json
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

    public static function paginate(Paginator $list): Json
    {
        return json([
            'code'    => Code::SUCCESS,
            'message' => 'success',
            'count'   => $list->total(),
            'current' => $list->currentPage(),
            'limit'   => $list->listRows(),
            'data'    => $list->getCollection(),
        ]);
    }
}

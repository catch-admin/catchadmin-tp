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
    public function success(mixed $data = [], string $message = '操作成功', int $code = Code::SUCCESS): Json
    {
        if ($data instanceof Paginator) {
            return $this->paginate($data);
        }

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
            'message' => '操作成功',
            'total'   => $list->total(),
            'limit'   => $list->listRows(),
            'data'    => $list->getCollection(),
        ]);
    }
}

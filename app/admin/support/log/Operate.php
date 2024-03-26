<?php
namespace app\admin\support\log;

use app\Request;
use app\admin\model\LogOperate;
use think\facade\Log;

class Operate
{
    /**
     * @param Request $request
     * @param mixed $response
     * @return void
     */
    public function handle(Request $request, mixed $response): void
    {
        try {
            $admin = $request->admin;

            if (!$admin) {
                return;
            }
            $rule = request()->rule()->getName();

            [$controller, $action] = explode('/', $rule);

            $controller = str_replace('app\admin\controller\\', '', $controller);

            $module = null;
            // 如果没有 \
            if (str_contains($controller, '\\')) {
                [$module, $controller] = explode('\\', $controller);
            }

            $requestStartAt = app()->getBeginTime() * 1000;
            $params = $request->all();
            // 如果参数过长则不记录
            if (!empty($params)) {
                if (strlen(\json_encode($params, JSON_UNESCAPED_UNICODE)) > 5000) {
                    $params = [];
                }
            }

            $timeTaken = intval(microtime(true) * 1000 - $requestStartAt);
            $operate = new LogOperate();
            $operate->storeBy([
                'module' => $module,
                'action' => $controller . '@' . $action,
                'creator_id' => $admin->id,
                'http_method' => $request->method(),
                'http_code' => $response->getCode(),
                'start_at' => intval($requestStartAt / 1000),
                'time_taken' => $timeTaken,
                'ip' => $request->ip(),
                'params' => \json_encode($params, JSON_UNESCAPED_UNICODE),
                'created_at' => time()
            ]);
        } catch (\Exception $e) {
            // do nothing
            Log::error('操作日志记录报错:' . $e->getMessage());
        }
    }
}

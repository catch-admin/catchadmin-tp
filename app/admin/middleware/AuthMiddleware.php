<?php
namespace app\admin\middleware;

use app\admin\support\CatchAuth;
use thans\jwt\facade\JWTAuth;
use think\Middleware;
use think\Request;
use thans\jwt\exception\TokenExpiredException;
use app\admin\exceptions\FailedException;
use thans\jwt\exception\TokenBlacklistException;
use thans\jwt\exception\TokenInvalidException;
use app\admin\support\enums\Code;

class AuthMiddleware extends Middleware
{
    public function handle(Request $request, \Closure $next)
    {
        try {
            JWTAuth::auth();
            // 将用户信息设置到请求中，以便后续使用
            $auth = new CatchAuth();
            $user = $auth->user();
            if (!$user) {
                throw new FailedException('登录用户不合法', Code::LOST_LOGIN);
            }
            $request->admin = $user;
        } catch (\Exception $e) {
            if ($e instanceof TokenExpiredException) {
                throw new FailedException('token 过期', Code::LOGIN_EXPIRED);
            }
            if ($e instanceof TokenBlacklistException) {
                throw new FailedException('token 被加入黑名单', Code::LOGIN_BLACKLIST);
            }
            if ($e instanceof TokenInvalidException) {
                throw new FailedException('token 不合法', Code::LOST_LOGIN);
            }

            throw new FailedException('登录用户不合法', Code::LOST_LOGIN);
        }

        return $next($request);
    }
}

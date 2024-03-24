<?php
declare(strict_types=1);

namespace app\admin\exceptions;

use app\admin\support\enums\Code;

class LoginFailedException extends CatchException
{
    protected $code = Code::LOGIN_FAILED;

    protected $message = '登录失败，请检查您的邮箱和密码';
}

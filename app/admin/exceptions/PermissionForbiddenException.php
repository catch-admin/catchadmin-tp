<?php
declare(strict_types=1);

namespace app\admin\exceptions;

use app\admin\support\enums\Code;

class PermissionForbiddenException extends CatchException
{
    protected $code = Code::PERMISSION_FORBIDDEN;

    protected $message = '权限不足, 请联系管理员';
}

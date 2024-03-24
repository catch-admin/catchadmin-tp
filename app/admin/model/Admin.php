<?php
declare(strict_types=1);

namespace app\admin\model;

class Admin extends CatchModel
{
    public function setPasswordAttr(string $value): string
    {
        return bcrypt($value);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->{$this->getPk()}, config('catch.super_admin'));
    }
}

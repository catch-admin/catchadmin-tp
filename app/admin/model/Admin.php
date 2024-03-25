<?php
declare(strict_types=1);

namespace app\admin\model;

class Admin extends CatchModel
{
    protected $field = [
        'username', 'password', 'email', 'status', 'avatar', 'created_at', 'updated_at', 'deleted_at', 'creator_id', 'remember_token',
    ];

    protected $hidden = ['password', 'remember_token', 'deleted_at'];

    /**
     * @param string $value
     * @return string
     */
    public function setPasswordAttr(string $value): string
    {
        return bcrypt($value);
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->{$this->getPk()}, config('catch.super_admin'));
    }
}

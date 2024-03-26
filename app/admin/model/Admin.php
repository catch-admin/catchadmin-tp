<?php
declare(strict_types=1);

namespace app\admin\model;

use app\admin\model\permissions\Roles;
use app\admin\model\permissions\traits\HasRolesTrait;
use app\admin\model\permissions\Permissions;
use app\admin\model\permissions\traits\HasJobsTrait;

class Admin extends CatchModel
{
    use HasRolesTrait, HasJobsTrait;

    protected $field = [
        'id', 'username', 'password', 'email', 'status', 'avatar', 'created_at', 'department_id', 'updated_at', 'deleted_at', 'creator_id', 'remember_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected array $autoWriteRelations = ['roles', 'jobs'];

    public array $searchable = [
        'status' => '=',
        'email' => 'like',
        'username' => 'like',
        'department_id' => '=',
    ];

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

    public function permissions(int $adminId = 0): array|static
    {
        // 获取超级管理配置 超级管理员全部权限
        if ($this->isSuperAdmin()) {
            $permissionIds = Permissions::select()->column('id');
        } else {
            $roles = $adminId ? $this->findBy($adminId)->getRoles() : $this->getRoles();

            $permissionIds = [];
            /* @var Roles $role */
            foreach ($roles as $role) {
                $permissionIds = array_merge($permissionIds, $role->getPermissions()->column('id'));
            }
        }

        $permissions = Permissions::whereIn('id', array_unique($permissionIds))->catchOrder()->select();
        /* @var  Permissions $permission */
        foreach ($permissions as $permission) {
            $permission->setAttr('hidden', $permission->isHidden());
        }

        $this->permissions = $permissions;
        $this->roles = $this->roles()->column('identify');
        return $this;
    }
}

<?php
namespace app\admin\model\permissions\traits;

use app\admin\model\permissions\Roles;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\model\relation\BelongsToMany;

trait HasRolesTrait
{
    /**
     * @return mixed
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'admin_has_roles', 'role_id', 'admin_id');
    }

    /**
     * @return array|\think\Collection|BelongsToMany[]
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getRoles(): \think\Collection|array
    {
        return $this->roles()->select();
    }

    /**
     * @param array $roles
     * @return array|\think\model\Pivot|true
     * @throws DbException
     */
    public function attachRoles(array $roles): bool|array|\think\model\Pivot
    {
        if (empty($roles)) {
            return true;
        }

        sort($roles);

        return $this->roles()->attach($roles);
    }

    /**
     * @param array $roles
     * @return int
     */
    public function detachRoles(array $roles = []): int
    {
        if (empty($roles)) {
            return $this->roles()->detach();
        }

        return $this->roles()->detach($roles);
    }
}

<?php

namespace app\admin\model\permissions\traits;

use app\admin\model\permissions\Permissions;
use think\model\relation\BelongsToMany;

trait HasPermissionsTrait
{
    /**
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permissions::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    /**
     * @param array $condition
     * @param array $field
     * @return array|\think\Collection|BelongsToMany[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPermissions(array $condition = [], array $field = [])
    {
        return $this->permissions()
            ->when(!empty($field), function ($query) use ($field){
                $query->field($field);
            })
            ->when(!empty($condition), function ($query) use ($condition){
                $query->where($condition);
            })
            ->select();
    }

    /**
     * @param array $permissions
     * @return array|\think\model\Pivot|true
     * @throws \think\db\exception\DbException
     */
    public function attachPermissions(array $permissions): bool|array|\think\model\Pivot
    {
        if (empty($permissions)) {
            return true;
        }

        sort($permissions);

        return $this->permissions()->attach($permissions);
    }

    /**
     * @param array $permissions
     * @return int
     */
    public function detachPermissions(array $permissions = []): int
    {
        if (empty($permissions)) {
            return $this->permissions()->detach();
        }

        return $this->permissions()->detach($permissions);
    }
}

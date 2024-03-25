<?php
namespace app\admin\model\permissions\traits;

use app\admin\model\permissions\Departments;
use think\model\relation\BelongsToMany;

trait HasDepartmentsTrait
{
    /**
     * @return BelongsToMany
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Departments::class, 'role_has_departments', 'department_id', 'role_id');
    }

    /**
     * @return array|\think\Collection|BelongsToMany[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDepartments()
    {
        return $this->departments()->select();
    }

    /**
     * @param array $departments
     * @return array|\think\model\Pivot|true
     * @throws \think\db\exception\DbException
     */
    public function attachDepartments(array $departments)
    {
        if (empty($departments)) {
            return true;
        }

        sort($departments);

        return $this->departments()->attach($departments);
    }

    /**
     * @param array $departments
     * @return int
     */
    public function detachDepartments(array $departments = [])
    {
        if (empty($departments)) {
            return $this->departments()->detach();
        }

        return $this->departments()->detach($departments);
    }
}

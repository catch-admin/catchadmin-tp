<?php
namespace app\admin\model\permissions\traits;

use app\admin\model\Admin;
use app\admin\model\permissions\Departments;
use app\admin\model\permissions\Roles;

trait DataRangScopeTrait
{
    /**
     * @param array $roles
     * @return $this
     */
    public function dataRange(array $roles = [])
    {
        if (request()->user->isSuperAdmin()) {
            return $this;
        }

        $userIds =  $this->getDepartmentUserIdsBy($roles);

        if (empty($userIds)) {
            return $this;
        }

        return $this->whereIn($this->aliasField('creator_id'), $userIds);
    }

    /**
     * @param $roles
     * @return array
     */
    public function getDepartmentUserIdsBy($roles): array
    {
        $userIds = [];

        $isAll = false;

        $user = request()->user;

        if (empty($roles)) {
            $roles = $user->getRoles();
        }

        foreach ($roles as $role) {
            switch ($role->data_range) {
                case Roles::ALL_DATA:
                    $isAll = true;
                    break;
                case Roles::SELF_CHOOSE:
                    $departmentIds = array_merge(array_column($role->getDepartments()->toArray(), 'id'));
                    $userIds = array_merge($userIds, $this->getUserIdsByDepartmentId($departmentIds));
                    break;
                case Roles::SELF_DATA:
                    $userIds[] = $user->id;
                    break;
                case Roles::DEPARTMENT_DOWN_DATA:
                    // 查一下下级部门
                    $departmentIds = Departments::where('parent_id', $user->department_id)->column('id');
                    $departmentIds[] = $user->department_id;
                    $userIds = array_merge([$user->id], $this->getUserIdsByDepartmentId($departmentIds));
                    break;
                case Roles::DEPARTMENT_DATA:
                    $userIds = array_merge($userIds, $this->getUserIdsByDepartmentId([$user->department_id]));
                    break;
                default:
                    break;
            }

            // 如果有全部数据 直接跳出
            if ($isAll) {
                break;
            }
        }

        return $userIds;
    }

    /**
     * @param array $id
     * @return array
     */
    protected function getUserIdsByDepartmentId(array $id): array
    {
        return Admin::whereIn('department_id', $id)->column('id');
    }
}

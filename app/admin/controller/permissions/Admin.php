<?php
namespace app\admin\controller\permissions;

use app\admin\controller\CatchController;
use app\admin\model\Admin as AdminModel;
use think\response\Json;

class Admin extends CatchController
{
    public function initialize(): void
    {
        $this->model = new AdminModel();
    }

    public function index()
    {
        return $this->success(
            $this->model->setBeforeGetList(function ($query){
                if (! $this->user()->isSuperAdmin()) {
                    $query = $query->whereNotIn('id', config('catch.super_admin'));
                }

                if (\request()->has('department_id')) {
                    $departmentId = \request()->get('department_id');
                    $followDepartmentIds = app(Departments::class)->findFollowDepartments(\request()->get('department_id'));
                    $followDepartmentIds[] = $departmentId;
                    $query = $query->whereIn('department_id', $followDepartmentIds);
                }

                return $query;
            })->getList()
        );
    }

    public function read($id)
    {
        $admin = $this->model->firstBy($id);

        $admin->setAttr('roles', $admin->roles()->select()->column('id'));
        $admin->setAttr('jobs', $admin->jobs()->select()->column('id'));

        return $this->success($admin);
    }

    public function online():Json
    {
        return $this->success($this->user()->permissions());
    }
}

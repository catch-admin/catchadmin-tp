<?php

namespace app\admin\controller\permissions;

use app\admin\controller\CatchController;
use app\admin\model\permissions\Roles as RolesModel;

class Roles extends CatchController
{
	public function initialize(): void
	{
		$this->model = new RolesModel();
	}

    public function index()
    {
        return $this->success(
            $this->model->setBeforeGetList(function ($query) {
                return $query->with('permissions', function ($query){
                    $query->select('id', 'name');
                });
            })->getList()
        );
    }

    public function save()
    {
        $data = $this->request->all();
        if (!isset($data['data_range'])) {
            $data['data_range'] = 0;
        } else {
            $data['data_range'] = (int)$data['data_range'];
            if (RolesModel::SELF_DATA != $data['data_range']) {
                $data['departments'] = [];
            }
        }

        return $this->success($this->model->storeBy($data));
    }

    public function read($id)
    {
        $role = $this->model->firstBy($id);

        if ($this->request->has('from') && $this->request->get('from') == 'parent_role') {
            $role->setAttr('permissions', $role->permissions()->select()->toTree());
        } else {
            $role->setAttr('permissions', $role->permissions()->select()->column('id'));
        }

        $role->setAttr('departments', $role->departments()->select()->column('id'));

        return $this->success($role);
    }

    public function update($id)
    {
        $data = $this->request->all();
        $data['data_range'] = (int) $data['data_range'];
        if (RolesModel::SELF_DATA != $data['data_range']) {
            $data['departments'] = [];
        }

        return $this->success($this->model->updateBy($id, $data));
    }
}

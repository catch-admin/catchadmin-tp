<?php

namespace app\admin\controller\permissions;

use app\admin\controller\CatchController;
use app\admin\model\permissions\Permissions as PermissionsModel;

class Permissions extends CatchController
{
	public function initialize(): void
	{
		$this->model = new PermissionsModel();
	}

    public function index()
    {
        if (request()->get('from') == 'role') {
            return $this->success($this->model->setBeforeGetList(function ($query){
                return $query->orderByDesc('sort');
            })->getList());
        }

        return $this->success($this->model->setBeforeGetList(function ($query) {
            return $query->with('actions')->whereIn('type', [PermissionsModel::TOP_MENU, PermissionsModel::MENU]);
        })->getList());
    }

    public function enable($id)
    {
        return $this->success($this->model->toggleBy($id, 'hidden'));
    }
}

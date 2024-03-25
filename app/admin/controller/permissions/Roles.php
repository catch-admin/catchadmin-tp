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
}

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
}

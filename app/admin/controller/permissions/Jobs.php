<?php

namespace app\admin\controller\permissions;

use app\admin\controller\CatchController;
use app\admin\model\permissions\Jobs as JobsModel;

class Jobs extends CatchController
{
	public function initialize(): void
	{
		$this->model = new JobsModel();
	}
}

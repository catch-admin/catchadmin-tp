<?php

namespace app\admin\model\permissions;

use app\admin\model\CatchModel;

class Departments extends CatchModel
{
	/**
	 * 可写入的字段
	 * @var string[]
	 */
	protected $field = [
		'id',
		'department_name',
		'parent_id',
		'principal',
		'mobile',
		'email',
		'creator_id',
		'status',
		'sort',
		'created_at',
		'updated_at',
		'deleted_at',
	];
}

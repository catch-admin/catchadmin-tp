<?php

namespace app\admin\model\permissions;

use app\admin\model\CatchModel;

class Jobs extends CatchModel
{
	/**
	 * 可写入的字段
	 * @var string[]
	 */
	protected $field = [
		'id',
		'job_name',
		'coding',
		'creator_id',
		'status',
		'sort',
		'description',
		'created_at',
		'updated_at',
		'deleted_at',
	];
}

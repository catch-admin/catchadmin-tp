<?php

namespace app\admin\model\permissions;

use app\admin\model\CatchModel;
use app\admin\model\permissions\traits\HasDepartmentsTrait;
use app\admin\model\permissions\traits\HasPermissionsTrait;

class Roles extends CatchModel
{
    use HasPermissionsTrait, HasDepartmentsTrait;

	/**
	 * 可写入的字段
	 * @var string[]
	 */
	protected $field = [
		'id',
		'role_name',
		'identifier',
		'parent_id',
		'description',
		'data_range',
		'creator_id',
		'created_at',
		'updated_at',
		'deleted_at',
	];

    public const ALL_DATA = 1; // 全部数据
    public const SELF_CHOOSE = 2; // 自定义数据
    public const SELF_DATA = 3; // 本人数据
    public const DEPARTMENT_DATA = 4; // 部门数据
    public const DEPARTMENT_DOWN_DATA = 5; // 部门及以下数据

}

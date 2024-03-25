<?php

namespace app\admin\model\permissions;

use app\admin\model\CatchModel;

class Permissions extends CatchModel
{
	/**
	 * 可写入的字段
	 * @var string[]
	 */
	protected $field = [
		'id',
		'permission_name',
		'parent_id',
		'route',
		'icon',
		'module',
		'permission_mark',
		'type',
		'active_menu',
		'component',
		'redirect',
		'hidden',
		'keepalive',
		'creator_id',
		'sort',
		'created_at',
		'updated_at',
		'deleted_at',
	];

    const HIDDEN = 2;
    const SHOW = 1;

    public function isHidden(): bool
    {
        return $this->hidden == self::SHOW;
    }
}

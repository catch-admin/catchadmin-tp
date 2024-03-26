<?php

namespace app\admin\model;

class LogOperate extends CatchModel
{
    protected $autoWriteTimestamp = false;

    protected bool $isSoftDelete = false;

	/**
	 * 可写入的字段
	 * @var string[]
	 */
	protected $field = [
		'id',
		'module',
		'action',
		'params',
		'ip',
		'http_method',
		'http_code',
		'start_at',
		'time_token',
		'creator_id',
		'created_at',
	];


    protected function getTimeTakenAttr($value): string
    {
        return $value > 1000 ? intval($value/1000) . 's' : $value . 'ms';
    }
}

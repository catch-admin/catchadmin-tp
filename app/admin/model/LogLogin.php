<?php

namespace app\admin\model;

class LogLogin extends CatchModel
{
    protected $autoWriteTimestamp = false;

    protected bool $isSoftDelete = false;

    /**
	 * 可写入的字段
	 * @var string[]
	 */
	protected $field = ['id', 'account', 'login_ip', 'browser', 'platform', 'login_at', 'status'];


    protected function getLoginAtAttr($value): string
    {
        return date('Y-m-d H:i', $value);
    }

    public function getUserLogBy(?string $email)
    {
        return static::when($email, function ($query) use ($email){
                 $query->where('account', $email);
             })
            ->order('id', 'desc')
            ->paginate(request()->get('limit', 10, 'int'));
    }
}

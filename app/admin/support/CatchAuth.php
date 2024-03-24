<?php

declare(strict_types=1);

namespace app\admin\support;

use app\admin\model\Admin;
use app\admin\exceptions\LoginFailedException;
use app\admin\support\enums\Code;
use app\admin\support\enums\Status;
use thans\jwt\facade\JWTAuth;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class CatchAuth
{
    /**
     * @var mixed
     */
    protected mixed $auth;

    // 默认获取
    protected string $username = 'email';

    // 校验字段
    protected string $password = 'password';

    // 保存用户信息
    protected ?Admin $user = null;

    /**
     * @var bool
     */
    protected bool $checkPassword = true;

    /**
     * @param $condition
     * @return string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function attempt($condition): string
    {
        /* @var Admin $user */
        $admin = Admin::where($this->filter($condition))->find();

        if (!$admin) {
            throw new LoginFailedException();
        }
        if ($admin->status == Status::DISABLE) {
            throw new LoginFailedException('该用户已被禁用|' . $admin->username, Code::USER_FORBIDDEN);
        }

        if ($this->checkPassword && !password_verify($condition['password'], $admin->password)) {
            throw new LoginFailedException('登录失败|' . $admin->username);
        }

        $token = $this->jwt($admin);

        // 保存 token
        $admin->remember_token = $token;
        $admin->login_at = time();
        $admin->login_ip = request()->ip();
        $admin->save();

        return $token;
    }


    /**
     * @return mixed
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function user(): mixed
    {
        if (!$this->user) {
            $model = new Admin();
            $user = $model->where($model->getPk(), JWTAuth::auth()[$this->jwtKey()])->find();
            $this->user = $user;
            return $user;
        }

        return $this->user;
    }

    /**
     * @param array $condition
     * @return array
     */
    public function filter(array $condition): array
    {
        $where = [];

        $fields = app(Admin::class)->getField();

        foreach ($condition as $field => $value) {
            if (in_array($field, $fields) && $field != $this->password) {
                $where[$field] = $value;
            }
        }

        return $where;
    }


    /**
     * @return true
     */
    public function logout(): bool
    {
        // 加入黑名单
        JWTAuth::invalidate(JWTAuth::token()->get());

        return true;
    }

    /**
     * @param $user
     * @return string
     */
    protected function jwt($user): string
    {
        $token = JWTAuth::builder([$this->jwtKey() => $user->id]);

        JWTAuth::setToken($token);

        return $token;
    }

    /**
     * @return string
     */
    protected function jwtKey(): string
    {
        return 'admin_jwt_id';
    }


    /**
     * @param $field
     * @return $this
     */
    public function username($field): static
    {
        $this->username = $field;

        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function password($field): static
    {
        $this->password = $field;

        return $this;
    }

    /**
     * @return $this
     */
    public function ignorePasswordVerify(): static
    {
        $this->checkPassword = false;

        return $this;
    }
}

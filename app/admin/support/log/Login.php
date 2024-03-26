<?php
namespace app\admin\support\log;

use app\admin\model\Admin;
use app\admin\support\enums\Status;
use app\Request;
use app\admin\model\LogLogin;

class Login
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Admin $admin = null, $token = null): void
    {
        $this->log((bool) $admin);

        if ($admin) {
            $admin->login_ip = request()->ip();
            $admin->login_at = time();
            $admin->remember_token = $token;
            $admin->save();
        }
    }

    /**
     * login log
     *
     * @param int $isSuccess
     * @return void
     */
    protected function log(int $isSuccess): void
    {
        LogLogin::insert([
            'account' => $this->request->post('email'),
            'login_ip' => $this->request->ip(),
            'browser' => $this->getBrowserFrom($this->request->header('user-agent')),
            'platform' => $this->getPlatformFrom($this->request->header('user-agent')),
            'login_at' => time(),
            'status' => $isSuccess ? Status::ENABLE : Status::DISABLE
        ]);
    }


    /**
     * get platform
     *
     * @param string $userAgent
     * @return string
     */
    protected function getBrowserFrom(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Edge') => 'Edge',
            str_contains($userAgent, 'MSIE') => 'IE',
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'Chrome') => 'Chrome',
            str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'Safari') => 'Safari',
            default => 'unknown'
        };
    }


    /**
     * get os name
     *
     * @param string $userAgent
     * @return string
     */
    protected function getPlatformFrom(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent,'win') => 'Window',
            str_contains($userAgent, 'mac') => 'Mac OS',
            str_contains($userAgent, 'linux') => 'Linux',
            str_contains($userAgent, 'iphone') => 'IPhone',
            str_contains($userAgent, 'android') => 'Android',
            default => 'unknown'
        };
    }
}

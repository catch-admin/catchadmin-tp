<?php
namespace app\admin\support;


use thans\jwt\contract\Storage;
use think\facade\Cache;

class JwtStorage implements Storage
{
    public function delete($key): bool
    {
        return Cache::delete($key);
    }

    public function get($key)
    {
        return Cache::get($key);
    }

    public function set($key, $val, $time = 0): bool
    {
        return Cache::set($key, $val, $time);
    }
}

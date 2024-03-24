<?php
// 应用公共文件

if (! function_exists('bcrypt')) {
    function bcrypt($value, $algo = PASSWORD_DEFAULT): string
    {
        return password_hash($value, $algo);
    }
}

<?php
declare(strict_types=1);

namespace app\admin\support;
use think\helper\Str;

class Helper
{
    /**
     * 字符串转换成数组
     *
     * @param string $string
     * @param string $dep
     * @return array
     */
    public static function stringToArrayBy(string $string, string $dep = ','): array
    {
        if (Str::contains($string, $dep)) {
            return explode($dep, trim($string, $dep));
        }

        return [$string];
    }



    /**
     * 表前缀
     *
     * @return mixed
     */
    public static function tablePrefix(): mixed
    {
        return \config('database.connections.mysql.prefix');
    }

    /**
     * 删除表前缀
     *
     * @param string $table
     * @return string|string[]
     */
    public static function tableWithoutPrefix(string $table): array|string
    {
        return str_replace(self::tablePrefix(), '', $table);
    }

    /**
     * 添加表前缀
     *
     * @param string $table
     * @return string
     */
    public static function tableWithPrefix(string $table): string
    {
        return Str::contains($table, self::tablePrefix()) ?
            $table : self::tablePrefix() . $table;
    }


    /**
     * public path
     *
     * @param string $path
     * @return string
     */
    public static function publicPath(string $path = ''): string
    {
        return root_path($path ? 'public/'. $path : 'public');
    }


    /**
     * 过滤空字符字段
     *
     * @param $data
     * @return mixed
     */
    public static function filterEmptyValue($data): mixed
    {
        foreach ($data as $k => $v) {
            if (!$v) {
                unset($data[$k]);
            }
        }

        return $data;
    }
}

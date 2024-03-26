<?php
namespace app\admin\support\options;

use app\admin\exceptions\FailedException;

class Factory
{
    public static function make(string $option): OptionInterface
    {
        $option = __NAMESPACE__ . '\\' . ucfirst($option);

        if (class_exists($option)) {
            $option = new $option();
            if (!$option instanceof OptionInterface) {
                throw new FailedException("{$option} 必须继承 OptionInterface");
            }

            return $option;
        }

        throw new FailedException("$option 没有找到，请在 support/options 目录下确认是否已添加");
    }
}

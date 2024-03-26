<?php
namespace app\admin\support\options;

use app\admin\support\enums\Status as StatusEnum;

class Status implements OptionInterface
{
    public function get(): array
    {
        return [
            [
                'value' => StatusEnum::ENABLE,
                'label' => '启用',
            ],
            [
                'value' => StatusEnum::DISABLE,
                'label' => '禁用',
            ]
        ];
    }
}

<?php
namespace app\admin\support\options;

use app\admin\model\permissions\Roles;

class DataRange implements OptionInterface
{

    public function get()
    {
        return [
            [
                'label' => '全部数据',
                'value' => Roles::ALL_DATA
            ],
            [
                'label' => '自定义数据',
                'value' => Roles::SELF_CHOOSE
            ],
            [
                'label' => '本人数据',
                'value' => Roles::SELF_DATA
            ],
            [
                'label' => '部门数据',
                'value' => Roles::DEPARTMENT_DATA
            ],
            [
                'label' => '部门及以下数据',
                'value' => Roles::DEPARTMENT_DOWN_DATA
            ]
        ];
    }
}

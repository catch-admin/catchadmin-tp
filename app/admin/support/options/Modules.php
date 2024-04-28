<?php
namespace app\admin\support\options;

class Modules implements OptionInterface
{

    public function get()
    {
        $modules = [
            [
                'label' => '默认',
                'value' => 'default'
            ]
        ];

        // TODO: Implement get() method.
        if (is_dir($path = app_path('admin/controller'))) {
            $data = scandir($path);
            foreach ($data as $value) {
                if($value != '.' && $value != '..' && is_dir($path . DIRECTORY_SEPARATOR .$value)){
                    $modules[] = [
                        'label' => $value,
                        'value' => $value
                    ];
                }
            }
        }

        return $modules;
    }
}

<?php
namespace app\admin\support\options;

use think\facade\Request;

class Controllers implements OptionInterface
{

    public function get()
    {
        $controller = [];
        $module = Request::get('module');
        if (!$module) {
            return $controller;
        }

        // TODO: Implement get() method.
        if (is_dir($path = app_path('admin/controller/' . (Request::get('module') == 'default' ? '' : Request::get('module'))))) {
            $data = scandir($path);
            foreach ($data as $value){
                if($value != '.' && $value != '..' && !is_dir($path . DIRECTORY_SEPARATOR .$value)){
                    $controllerName =  pathinfo($path . DIRECTORY_SEPARATOR .$value, PATHINFO_FILENAME);
                    if (in_array($controllerName, $this->notContains())) {
                        continue;
                    }

                    $controller[] = [
                        'label' => $controllerName,
                        'value' => $controllerName,
                    ];
                }
            }
        }

        return $controller;
    }


    protected function notContains(): array
    {
        return ['Auth', 'CatchController', 'Generate'];
    }
}

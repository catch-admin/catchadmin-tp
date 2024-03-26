<?php
namespace app\admin\support\options;

class Components implements OptionInterface
{

    public function get(): array
    {
        $module = request()->get('module');

        if (!$module) {
            return [];
        }

        // TODO: Implement get() method.
        $webViewsPath = config('catch.web_path')  . 'src' . DIRECTORY_SEPARATOR . 'views';

        $components = [];

        if (is_dir($path = $webViewsPath  . DIRECTORY_SEPARATOR . lcfirst($module) )) {

            $this->allVueFile($path, $components);

            foreach ($components as $k => $component) {
                $c = str_replace('\\', '/', str_replace($path, '', $component));

                $components[$k] = [
                    'label' => $c,
                    'value' => $c
                ];
            }
        }

        return $components;

    }


    protected function allVueFile($path, &$files = [])
    {
        $dirs =glob($path . DIRECTORY_SEPARATOR . '*');

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $this->allVueFile($dir, $files);
            }

            if (is_file($dir) && pathinfo($dir, PATHINFO_EXTENSION) == 'vue') {
                $files[] = $dir;
            }
        }
    }
}

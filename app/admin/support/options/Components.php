<?php
namespace app\admin\support\options;

class Components implements OptionInterface
{

    public function get(): array
    {
        $module = request()->get('module');
        $controller = lcfirst(request()->get('controller', ''));
        if (!$module) {
            return [];
        }

        // TODO: Implement get() method.
        $webViewsPath = config('catch.views_path');

        $components = [];

        if (is_dir($path = $webViewsPath . ($this->isDefaultModule() ? $controller : lcfirst($module)) )) {

            $this->allVueFile($path, $components);

            foreach ($components as $k => $component) {
                $c = str_replace('\\', '/', str_replace($path, '', $component));

                if ($this->isDefaultModule() && $controller) {
                    $c = '/'. $controller . $c;
                }

                $components[$k] = [
                    'label' => $c,
                    'value' => $c
                ];
            }
        }

        return $components;

    }

    protected function isDefaultModule(): bool
    {
        return request()->get('module') === 'default';
    }

    protected function allVueFile($path, &$files = []): void
    {
        $dirs = glob($path . DIRECTORY_SEPARATOR . '*');

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

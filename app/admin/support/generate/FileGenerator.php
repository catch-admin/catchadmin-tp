<?php
namespace app\admin\support\generate;

use think\facade\Log;

abstract class FileGenerator
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract public function generate();

    public function parseName(): array
    {
        if (str_contains($this->name,'\\')) {
            [$module, $name]  = explode('\\', $this->name);
            return [lcfirst($module), ucfirst($name)];
        } elseif (str_contains($this->name,'/')) {
            [$module, $name]  = explode('/', $this->name);
            return [lcfirst($module), ucfirst($name)];
        } else {
            return [null, ucfirst($this->name)];
        }
    }

    protected function put($path, $filename, $content): string|bool
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file = $path . $filename . '.php';
        if (file_put_contents($file, $content)) {
            return file_exists($file) ? $file : false;
        }

        return false;
    }

    protected function getViewPath(): string
    {
        $viewPath = config('catch.views_path');
        [$first, $second] = $this->parseName();
        if ($first) {
            $viewPath = $viewPath . lcfirst($first).DIRECTORY_SEPARATOR.lcfirst($second);
        } else {
            $viewPath = $viewPath . lcfirst($second);
        }

        if (!is_dir($viewPath)) {
            mkdir($viewPath, 0777, true);
        }

        return $viewPath;
    }

    /**
     * form components
     *
     * @return array
     */
    protected function formComponents(): array
    {
        $components = [];

        foreach (glob(
                     $this->getFormItemStub()
                 ) as $stub) {
            $components[pathinfo($stub, PATHINFO_FILENAME)] = file_get_contents($stub);
        }

        return $components;
    }

    public function parseComment($comment)
    {
        if (str_contains($comment, ':')) {
            return explode(': ', $comment)[0];
        }

        return $comment;
    }
}

<?php
namespace app\admin\support\generate;

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
}

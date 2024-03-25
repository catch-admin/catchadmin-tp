<?php
namespace app\admin\support\generate;

use app\admin\controller\CatchController;
use Nette\PhpGenerator\PhpFile;

class Controller extends FileGenerator
{
    protected ?string $model = null;

    protected ?string $modelNamespace = null;

    public function generate(): bool|string
    {
        [$module, $name] = $this->parseName();

        $controllerFile = new PhpFile();

        $controllerNamespace = 'app\admin\controller' . ($module ? "\\$module" : '') . '\\' . $name;

        $namespace = $controllerFile->addNamespace('app\admin\controller' . ($module ? "\\$module" : ''));

        if ($module) {
            $namespace->addUse(CatchController::class);
        }

        if ($this->model) {
            $namespace->addUse($this->modelNamespace, $this->model);
        }

        $class = $namespace->addClass($name);

        $class->setExtends(CatchController::class);

        $method = $class->addMethod('initialize')->setReturnType(
            'void'
        );

        if ($this->model) {
            $method->addBody('$this->model = new ' . $this->model . '();');
        }

        $this->put(app_path('admin/controller/' . ($module ? $module . '/' : '')), $name, $controllerFile);

        return $controllerNamespace;
    }

    public function setModel(string $model)
    {
        $this->modelNamespace  = $model;

        $model = explode('\\', $this->modelNamespace);

        $this->model = ucfirst(end($model) . 'Model');

        return $this;
    }
}

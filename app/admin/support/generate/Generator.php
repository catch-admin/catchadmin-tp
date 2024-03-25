<?php

namespace app\admin\support\generate;

class Generator
{
    protected string $controllerName;

    protected string $modelName;

    protected string $table;

    public function __construct(string $controllerName, $modelName, string $table = '')
    {
        $this->controllerName = $controllerName;

        $this->modelName = $modelName;

        $this->table = $table;
    }

    public function generate(): array
    {
        $model = new Model($this->modelName);

        $modelNamespace = $model->setTable($this->table)->generate();

        $controller = new Controller($this->controllerName);

        $controllerNamespace = $controller->setModel($modelNamespace)->generate();

        $route = new Route('');

        $route->setController($controllerNamespace)->generate();

        return [
            $controllerNamespace,
            $modelNamespace
        ];
    }
}

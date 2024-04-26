<?php

namespace app\admin\support\generate;

class Generator
{
    protected string $controllerName;

    protected string $modelName;

    protected string $table;

    protected array $schema;

    public function __construct(string $controllerName, $modelName, string $table = '', $params = [])
    {
        $this->controllerName = $controllerName;

        $this->modelName = $modelName;

        $this->table = $table;

        $this->schema = $params;
    }

    public function generate(): array
    {
        $schema = new Schema();

        $schema->setSchema($this->schema)->generate();

        $model = new Model($this->modelName ?  : $this->table);

        $modelNamespace = $model->setTable($this->table)->generate();

        $controllerNamespace = '';
        if ($this->controllerName) {
            $controller = new Controller($this->controllerName);

            $controllerNamespace = $controller->setModel($modelNamespace)->generate();

            $route = new Route('');

            $route->setController($controllerNamespace)->generate();
        }

        return [
            $controllerNamespace,
            $modelNamespace
        ];
    }
}

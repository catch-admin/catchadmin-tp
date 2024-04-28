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
        if (!app()->runningInConsole()) {
            $schema = new Schema();

            $schema->setSchema($this->schema)->generate();
        }

        $model = new Model($this->modelName ?  : $this->table);

        $modelNamespace = $model->setTable($this->table)->generate();

        $controllerNamespace = $tableFile = $formFile = '';
        if ($this->controllerName) {
            $controller = new Controller($this->controllerName);

            $controllerNamespace = $controller->setModel($modelNamespace)->generate();

            $route = new Route('');

            $route->setController($controllerNamespace)->generate();

            // 生成前端
            if (!app()->runningInConsole()) {
                $fields = $this->schema['fields'];
                $frontTable = new FrontTable($this->controllerName, $this->schema['paginate'], $route->getRouteApi());
                $tableFile = $frontTable->setStructures($fields)->generate();
                $frontForm = new FrontForm($this->controllerName);
                $formFile = $frontForm->setStructures($fields)->generate();
            }
        }

        return [
            $controllerNamespace,
            $modelNamespace,
            $tableFile,
            $formFile
        ];
    }
}

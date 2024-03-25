<?php
namespace app\admin\support\generate;

use app\admin\model\CatchModel;
use Nette\PhpGenerator\PhpFile;
use think\facade\Db;

class Model extends FileGenerator
{
    protected  string $table;

    public function generate(): bool|string
    {
        [$module, $name] = $this->parseName();

        $modelFile = new PhpFile();

        $namespaceModel = 'app\admin\model' . ($module ? "\\$module" : '') . '\\' . $name;

        $namespace = $modelFile->addNamespace('app\admin\model' . ($module ? "\\$module" : ''));

        if ($module) {
            $namespace->addUse(CatchModel::class);
        }

        $class = $namespace->addClass($name);

        $class->setExtends(CatchModel::class);

        $class->addProperty('field', array_keys(Db::getFields($this->table)))->setProtected()->addComment('可写入的字段')->addComment('@var string[]');

        $this->put(app_path('admin/model/' . ($module ? $module . '/' : '')), $name, $modelFile);

        return $namespaceModel;
    }

    public function setTable(string $table): static
    {
        $this->table = $table;

        return $this;
    }
}

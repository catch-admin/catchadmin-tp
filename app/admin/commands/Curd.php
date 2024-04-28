<?php
namespace app\admin\commands;

use app\admin\support\generate\Route;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use app\admin\support\generate\Generator;


class Curd extends Command
{

    protected function configure()
    {
        $this->setName('catch:curd')
            ->addArgument('table', Option::VALUE_REQUIRED, '表名')
            ->addOption('model', '-m', Option::VALUE_OPTIONAL, '模型名称')
            ->addOption('controller', '-c', Option::VALUE_OPTIONAL, '控制器名称')
            ->addOption('delete', '-d', Option::VALUE_OPTIONAL, '删除文件')
            ->addOption('force', '-f', Option::VALUE_OPTIONAL, '强制覆盖')
            ->setDescription('create controller');
    }

    public function execute(Input $input, Output $output)
    {
        $table = $this->input->getArgument('table');

        $modelFromTable = $this->parseTable($table);

        $model = $this->input->getOption('model');
        $controller = $this->input->getOption('controller');
        $delete = $this->input->hasOption('delete');
        $force = $this->input->hasOption('force');

        if (!$model) {
            $model = $modelFromTable;
        }

        if (!$controller) {
            $controller = $model;
        }

        $generator = new Generator($controller, $model, $table);

        $res = $generator->generate();

        foreach ($res as $value) {
            $this->output->info($value . ' 创建成功');
        }
    }


    protected function parseTable($table): string
    {
        $table = tableWithoutPrefix($table);

        $model = '';

        if (str_contains($table, '_')) {
            foreach (explode('_', $table) as $value) {
                $model .= ucfirst($value);
            }
        } else {
            $model = ucfirst($table);
        }

        return $model;
    }
}

<?php
namespace app\admin\commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Model extends Command
{

    protected function configure()
    {
        $this->setName('catch:model')
            ->addArgument('name', Option::VALUE_REQUIRED, 'model name')
            ->addOption('table', '-t', Option::VALUE_OPTIONAL, 'table name')
            ->setDescription('create controller');
    }

    public function execute(Input $input, Output $output)
    {
        $name = $this->input->getArgument('name');

        $table = $this->input->getOption('table');

        $model = new \app\admin\support\generate\model($name);

        $model->setTable($table);

        file_put_contents('aaa.php', $model->generate());
    }
}

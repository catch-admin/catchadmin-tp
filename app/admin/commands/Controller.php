<?php
namespace app\admin\commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class Controller extends Command
{

    protected function configure(): void
    {
        $this->setName('catch:controller')
            ->addArgument('name', Option::VALUE_REQUIRED, 'controller name')
            ->setDescription('create controller');
    }

    public function execute(Input $input, Output $output): void
    {
        $name = $this->input->getArgument('name');

        $controller = new \app\admin\support\generate\Controller($name);

        file_put_contents('aaa.php', $controller->generate());
    }
}

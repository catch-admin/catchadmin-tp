<?php
namespace app\admin\commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Console;
use think\helper\Str;

class Install extends Command
{
    protected string $webRepo = 'https://gitee.com/catchadmin/catch-admin-vue.git';
    protected array $databaseLink = [];

    protected string $appDomain = '';

    protected function configure()
    {
        $this->setName('catch:install')
            ->addOption('reinstall', '-r',Option::VALUE_NONE, 'reinstall back')
            ->setDescription('install project');
    }

    protected function execute(Input $input, Output $output): void
    {
        if ($input->getOption('reinstall')) {
            $this->reInstall();
            $this->project();
        } else {

            $this->detectionEnvironment();

            $this->firstStep();

            $this->secondStep();

            $this->finished();

            $this->project();
        }
    }

    protected function detectionEnvironment(): void
    {
        $this->output->info('环境检测...');

        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
            $this->output->error('php version should >= 8.0.0');
            exit();
        }

        $this->output->info('php 版本 ' . PHP_VERSION);

        if (!extension_loaded('mbstring')) {
            $this->output->error('mbstring extension not install');exit();
        }

        if (!extension_loaded('json')) {
            $this->output->error('json extension not install');
            exit();
        }

        if (!extension_loaded('openssl')) {
            $this->output->error('openssl extension not install');
            exit();
        }

        if (!extension_loaded('pdo')) {
            $this->output->error('pdo extension not install');
            exit();
        }

        if (!extension_loaded('xml')) {
            $this->output->error('xml extension not install');
            exit();
        }

        $this->output->info('🎉 环境检测完成');
    }


    protected function firstStep()
    {
        if (file_exists($this->app->getRootPath() . '.env')) {
            return false;
        }

        // 设置 app domain
        $appDomain = strtolower($this->output->ask($this->input, '👉首先需要设置后端访问的域名(开发环境例如 http://127.0.0.1:8000): '));
        if ($appDomain) {
            $appDomain = 'http://127.0.0.1:8000';
        } else {
            if (!str_contains($appDomain, 'http')) {
                $appDomain = 'http://' . $appDomain;
            }
        }
        $this->appDomain = $appDomain;

        $answer = strtolower($this->output->ask($this->input, '🤔️ 设置数据库信息? (Y/N): '));

        if ($answer === 'y' || $answer === 'yes') {
            $charset = $this->output->ask($this->input, '👉 设置数据库编码集, 默认 (utf8mb4):') ?: 'utf8mb4';
            $database = '';
            while (!$database) {
                $database = $this->output->ask($this->input, '👉 设置数据库名称: ');
                if ($database) {
                    break;
                }
            }
            $host = $this->output->ask($this->input, '👉 设置数据库 Host, 默认 (127.0.0.1):') ?: '127.0.0.1';
            $port = $this->output->ask($this->input, '👉 设置数据库端口号, 默认 (3306):') ?: '3306';
            // $prefix = $this->output->ask($this->input, '👉 please input table prefix, default (null):') ? : '';
            $username = $this->output->ask($this->input, '👉设置数据库用户名，默认 (root): ') ?: 'root';
            $password = '';
            $tryTimes = 0;
            while (!$password) {
                $password = $this->output->ask($this->input, '👉 设置数据库密码: ');
                if ($password) {
                    break;
                }
                // 尝试三次以上未填写，视为密码空
                $tryTimes++;
                if (!$password && $tryTimes > 2) {
                    break;
                }
            }

            $this->databaseLink = [$host, $database, $username, $password, $port, $charset];

            $this->generateEnvFile($host, $database, $username, $password, $port, $charset, $appDomain);
        }
    }

    protected function secondStep(): void
    {
        if (file_exists($this->getEnvFilePath())) {
            $connections = \config('database.connections');
            // 因为 env file 导致安装失败
            if (!$this->databaseLink) {
                unlink($this->getEnvFilePath());
                $this->execute($this->input, $this->output);
            } else {

                [
                    $connections['mysql']['hostname'],
                    $connections['mysql']['database'],
                    $connections['mysql']['username'],
                    $connections['mysql']['password'],
                    $connections['mysql']['hostport'],
                    $connections['mysql']['charset'],
                    // $connections['mysql']['prefix'],
                ] = $this->databaseLink ?: [
                    env('mysql.hostname')
                ];

                \config([
                    'connections' => $connections,
                ], 'database');

            }

            Console::call('migrate:run');
            Console::call('seed:run');
        }
    }


    protected function finished(): void
    {
        // todo something
        // create jwt
        Console::call('jwt:create');

        $this->cloneWeb();
    }


    protected function generateEnvFile($host, $database, $username, $password, $port, $charset, $appDomain): void
    {
        try {
            $env = \parse_ini_file(root_path() . '.example.env', true);

            $env['APP_HOST'] = $appDomain;
            $env['DB_HOST'] = $host;
            $env['DB_NAME'] = $database;
            $env['DB_USER'] = $username;
            $env['DB_PASS'] = $password;
            $env['DB_PORT'] = $port;
            $env['DB_CHARSET'] = $charset;

            # JWT 密钥
            $env['JWT_SECRET'] = md5(Str::random(8));

            $dotEnv = '';
            foreach ($env as $key => $e) {
                if (is_string($e)) {
                    $dotEnv .= sprintf('%s=%s', $key, $e === '1' ? 'true' : ($e === '' ? 'false' : $e));
                    $dotEnv .= PHP_EOL;
                } else {
                    $dotEnv .= sprintf('[%s]', $key);
                    foreach ($e as $k => $v) {
                        $dotEnv .= sprintf('%s=%s', $k, $v === '1' ? 'true' : ($v === '' ? 'false' : $v)) ;
                    }

                    $dotEnv .= PHP_EOL;
                }
            }


            if ($this->getEnvFile()) {
                $this->output->info('env 环境变量文件已被创建');
            }
            if ((new \mysqli($host, $username, $password, null, $port))->query(sprintf('CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARSET %s COLLATE %s_general_ci;',
                $database, $charset, $charset))) {
                $this->output->info(sprintf('🎉创建数据库 %s 成功', $database));
            } else {
                $this->output->warning(sprintf('创建数据库 %s 失败，你需要手动创建对应数据库', $database));
            }
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
            exit(0);
        }

        file_put_contents(root_path() . '.env', $dotEnv);
    }

    protected function getEnvFile(): string
    {
        return file_exists(root_path() . '.env') ? root_path() . '.env' : '';
    }


    protected function project()
    {
        $domain = explode($this->appDomain, ':');
        $port = end($domain);

        $year = date('Y');

        $this->output->info('🎉 项目已安装, welcome!');

        $this->output->info(sprintf('
 /-------------------- welcome to use -------------------------\                     
|               __       __       ___       __          _      |
|   _________ _/ /______/ /_     /   | ____/ /___ ___  (_)___  |
|  / ___/ __ `/ __/ ___/ __ \   / /| |/ __  / __ `__ \/ / __ \ |
| / /__/ /_/ / /_/ /__/ / / /  / ___ / /_/ / / / / / / / / / / |
| \___/\__,_/\__/\___/_/ /_/  /_/  |_\__,_/_/ /_/ /_/_/_/ /_/  |
|                                                              |   
 \ __ __ __ __ _ __ _ __ enjoy it ! _ __ __ __ __ __ __ ___ _ @ 2017 ～ %s
 初始账号: catch@admin.com
 初始密码: catchadmin
 后端启动: php think run --port=%d
 前端启动: cd web && yarn dev                                      
', $year, intval($port)));
        exit(0);
    }


    protected function reInstall(): void
    {
        $ask = strtolower($this->output->ask($this->input,'reset project? (Y/N)'));

        if ($ask === 'y' || $ask === 'yes' ) {
            Console::call('migrate:rollback');

            if (file_exists($this->getEnvFilePath())) {
                unlink($this->getEnvFilePath());
            }
        }
    }

    /**
     * 获取 env path
     *
     * @time 2020年04月06日
     * @return string
     */
    protected function getEnvFilePath(): string
    {
        return root_path() . '.env';
    }


    protected function cloneWeb(): void
    {
        $webPath = $this->app->getRootPath(). DIRECTORY_SEPARATOR . 'web';

        if (! is_dir($webPath)) {
            $this->output->info('下载前端项目');

            shell_exec("git clone {$this->webRepo} web");

            if (is_dir($webPath)) {
                $this->output->info('下载前端项目成功');
                $this->output->info('设置镜像源');
                shell_exec('yarn config set registry https://registry.npmmirror.com');
                $this->output->info('安装前端依赖，如果安装失败，请检查是否已安装了前端 yarn 管理工具，或者因为网络等原因');
                shell_exec('cd ' . $this->app->getRootPath() . DIRECTORY_SEPARATOR . 'web && yarn install');
                $this->output->info('手动启动使用 yarn dev');
                $this->output->info('项目启动后不要忘记设置 web/.env 里面的环境变量 VITE_BASE_URL');
                $this->output->info('安装前端依赖成功，开始启动前端项目');
                file_put_contents($webPath . DIRECTORY_SEPARATOR . '.env', <<<STR
VITE_BASE_URL=$this->appDomain/api
VITE_APP_NAME=后台管理
VITE_GENERATE=true
STR
);
                // shell_exec("cd {$webPath} && yarn dev");
            } else {
                $this->output->error('下载前端项目失败, 请到该仓库下载 https://gitee.com/catchadmin/catch-admin-vue');
            }
        }
    }
}

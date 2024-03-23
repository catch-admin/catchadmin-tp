<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter;

class Init extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->admin();

        $this->roles();

        $this->permissions();

        $this->adminHasRoles();

        $this->roleHasPermissions();

        $this->jobs();

        $this->departments();

        $this->adminHasJobs();

        $this->roleHasDepartments();

        $this->loginLog();

        $this->operateLog();
    }

    protected function admin()
    {
        if (!$this->hasTable('admin')) {
            $table = $this->table('admin', [
                'id'          => false,
                'primary_key' => 'id',
                'collation'   => 'utf8mb4_unicode_ci',
            ]);
            $table->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('username', 'string', ['limit' => 50, 'comment' => '昵称', 'null' => false])
                ->addColumn('password', 'string', ['limit' => 255, 'comment' => '密码', 'null' => false])
                ->addColumn('email', 'string', ['limit' => 50, 'comment' => '邮箱', 'null' => false])
                ->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '头像', 'null' => false])
                ->addColumn('remember_token', 'string', ['limit' => 1000, 'default' => '', 'comment' => '登录令牌'])
                ->addColumn('department_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'comment' => '部门ID', 'null' => false])
                ->addColumn('creator_id', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'comment' => '创建人ID', 'null' => false])
                ->addColumn('status', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 1, 'comment' => '状态:1正常 2禁用', 'null' => false])
                ->addColumn('login_ip', 'string', ['limit' => 50, 'default' => '', 'comment' => '登录IP', 'null' => false])
                ->addColumn('login_at', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'comment' => '登录时间', 'null' => false])
                ->addColumn('created_at', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                ->addColumn('updated_at', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'comment' => '更新时间', 'null' => false])
                ->addColumn('deleted_at', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_REGULAR, 'default' => 0, 'comment' => '删除时间', 'null' => false])
                ->setComment('管理员表')
                ->create();
        }
    }

    protected function roles()
    {

        if (!$this->hasTable('roles')) {
            $table  =  $this->table('roles',['engine'=>'Innodb', 'comment' => '角色表', 'signed' => false]);
            $table->addColumn('role_name', 'string',['limit'  =>  15,'default'=>'','comment'=>'角色名'])
                ->addColumn('identifier', 'string',['limit'  =>  30,'default'=>'','comment'=>'角色标识'])
                ->addColumn('parent_id', 'integer',['default'=>0,'comment'=>'父级ID', 'signed' => false])
                ->addColumn('description', 'string',['default'=> '','comment'=>'角色备注'])
                ->addColumn('data_range', 'integer',['limit' => MysqlAdapter::INT_TINY,'default'=> 0,'comment'=>'1 全部数据 2 自定义数据 3 仅本人数据 4 部门数据 5 部门及以下数据'])
                ->addColumn('creator_id', 'integer',['default' => 0, 'comment'=>'创建人ID'])
                ->addColumn('created_at', 'integer', array('default'=>0,'comment'=>'创建时间', 'signed' => false ))
                ->addColumn('updated_at', 'integer', array('default'=>0,'comment'=>'更新时间', 'signed' => false))
                ->addColumn('deleted_at', 'integer', array('default'=>0,'comment'=>'删除状态，0未删除 >0 已删除', 'signed' => false))
                ->create();
        }
    }

    protected function permissions()
    {
        if (!$this->hasTable('permissions')) {
            $table  =  $this->table('permissions',['engine'=>'Innodb', 'comment' => '菜单表', 'signed' => false]);
            $table->addColumn('permission_name', 'string',['limit'  =>  15,'default'=>'','comment'=>'菜单名称'])
                  ->addColumn('parent_id', 'integer',['default'=>0,'comment'=>'父级ID', 'signed' => false])
                  ->addColumn('route', 'string', ['default' => '', 'comment' => '路由', 'limit' => 50])
                  ->addColumn('icon', 'string', ['default' => '', 'comment' => '菜单图标', 'limit' => 50])
                  ->addColumn('module', 'string', ['default' => '', 'comment' => '模块', 'limit' => 20])
                  ->addColumn('permission_mark', 'string', ['null' => false, 'comment' => '权限标识', 'limit' => 50])
                  ->addColumn('type', 'integer',['limit' => MysqlAdapter::INT_TINY,'default'=> 1,'comment'=>'1 菜单 2 按钮'])
                  ->addColumn('active_menu', 'string', ['null' => false, 'comment' => '权限标识', 'limit' => 100, 'default' => ''])
                  ->addColumn('component', 'string', ['default' => '', 'comment' => '组件名称', 'limit' => '255'])
                  ->addColumn('redirect', 'string', ['default' => '', 'comment' => '跳转地址', 'limit' => '255'])
                  ->addColumn('hidden', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 1, 'comment' => '1 显示 2隐藏'])
                  ->addColumn('keepalive', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => 1, 'comment' => '1 缓存 2 不存在 '])
                  ->addColumn('creator_id', 'integer',['default' => 0, 'comment'=>'创建人ID'])
                  ->addColumn('sort', 'integer',['default'=> 0,'comment'=>'排序字段'])
                  ->addColumn('created_at', 'integer', array('default'=>0,'comment'=>'创建时间', 'signed' => false ))
                  ->addColumn('updated_at', 'integer', array('default'=>0,'comment'=>'更新时间', 'signed' => false))
                  ->addColumn('deleted_at', 'integer', array('default'=>0,'comment'=>'删除状态，null 未删除 timestamp 已删除', 'signed' => false))
                  ->create();
        }
    }

    protected function adminHasRoles()
    {
        if (!$this->hasTable('admin_has_roles')) {
            $table  =  $this->table('admin_has_roles',['engine'=>'Innodb', 'comment' => '管理员角色关联表', 'signed' => false]);
            $table->addColumn('admin_id', 'integer',['default'=>0,'comment'=>'用户ID', 'signed' => false])
                  ->addColumn('role_id', 'integer',['default'=>0,'comment'=>'角色ID', 'signed' => false])
                  ->create();
        }
    }


    protected function roleHasPermissions()
    {
        if (!$this->hasTable('role_has_permissions')) {
            $table  =  $this->table('role_has_permissions',['engine'=>'Innodb', 'comment' => '角色权限关联表', 'signed' => false]);
            $table->addColumn('role_id', 'integer',['default'=>0,'comment'=>'角色ID', 'signed' => false])
                ->addColumn('permission_id', 'integer',['default'=>0,'comment'=>'权限ID', 'signed' => false])
                ->create();
        }
    }

    protected function jobs()
    {
        if (!$this->hasTable('jobs')) {
            $table  =  $this->table('jobs',['engine'=>'Innodb', 'comment' => '岗位表', 'signed' => false]);
            $table->addColumn('job_name', 'string',['limit'  =>  15,'default'=>'','comment'=>'岗位名称'])
                ->addColumn('coding', 'string', ['default' => '', 'comment' => '编码', 'limit' => 50])
                ->addColumn('creator_id', 'integer',['default' => 0, 'comment'=>'创建人ID'])
                ->addColumn('status', 'integer',['limit' => MysqlAdapter::INT_TINY,'default'=> 1,'comment'=>'1 正常 2 停用'])
                ->addColumn('sort', 'integer',['default'=> 0,'comment'=>'排序字段'])
                ->addColumn('description', 'string', ['default' => '', 'comment' => '描述', 'limit' => 255])
                ->addColumn('created_at', 'integer', array('default'=>0,'comment'=>'创建时间', 'signed' => false ))
                ->addColumn('updated_at', 'integer', array('default'=>0,'comment'=>'更新时间', 'signed' => false))
                ->addColumn('deleted_at', 'integer', array('default'=>0,'comment'=>'删除状态，null 未删除 timestamp 已删除', 'signed' => false))
                ->create();
        }
    }

    protected function departments(): void
    {
        if (!$this->hasTable('departments')) {
            $table  =  $this->table('departments',['engine'=>'Innodb', 'comment' => '岗位表', 'signed' => false]);
            $table->addColumn('department_name', 'string',['limit'  =>  15,'default'=>'','comment'=>'部门名称'])
                ->addColumn('parent_id', 'integer',['default'=>0,'comment'=>'父级ID', 'signed' => false])
                ->addColumn('principal', 'string', ['default' => '', 'comment' => '负责人', 'limit' => 20])
                ->addColumn('mobile', 'string', ['default' => '', 'comment' => '联系电话', 'limit' => 20])
                ->addColumn('email', 'string', ['default' => '', 'comment' => '联系又想', 'limit' => 100])
                ->addColumn('creator_id', 'integer',['default' => 0, 'comment'=>'创建人ID'])
                ->addColumn('status', 'integer',['limit' => MysqlAdapter::INT_TINY,'default'=> 1,'comment'=>'1 正常 2 停用'])
                ->addColumn('sort', 'integer',['default'=> 0,'comment'=>'排序字段'])
                ->addColumn('created_at', 'integer', array('default'=>0,'comment'=>'创建时间', 'signed' => false ))
                ->addColumn('updated_at', 'integer', array('default'=>0,'comment'=>'更新时间', 'signed' => false))
                ->addColumn('deleted_at', 'integer', array('default'=>0,'comment'=>'删除状态，null 未删除 timestamp 已删除', 'signed' => false))
                ->create();
        }
    }


    protected function adminHasJobs(): void
    {
        if (!$this->hasTable('admin_has_jobs')) {
            $table  =  $this->table('admin_has_jobs',['engine'=>'Innodb', 'comment' => '管理员岗位关联表', 'signed' => false]);
            $table->addColumn('admin_id', 'integer',['default'=>0,'comment'=>'用户ID', 'signed' => false])
                ->addColumn('job_id', 'integer',['default'=>0,'comment'=>'岗位ID', 'signed' => false])
                ->create();
        }
    }

    protected function roleHasDepartments(): void
    {
        if (!$this->hasTable('role_has_departments')) {
            $table  =  $this->table('role_has_departments',['engine'=>'Innodb', 'comment' => '角色部门关联表', 'signed' => false]);
            $table->addColumn('role_id', 'integer',['default'=>0,'comment'=>'用户ID', 'signed' => false])
                ->addColumn('department_id', 'integer',['default'=>0,'comment'=>'部门ID', 'signed' => false])
                ->create();
        }
    }

    protected function loginLog(): void
    {
        if(!$this->hasTable('log_login')) {
            $table  =  $this->table('log_login',['engine'=>'Innodb', 'comment' => '登录日志表', 'signed' => false]);
            $table->addColumn('account', 'string',['limit'  =>  100,'default'=>'','comment'=>'账号'])
                ->addColumn('login_ip', 'string',['limit'  =>  30,'default'=>'','comment'=>'登录IP'])
                ->addColumn('browser', 'string',['limit'  =>  20,'default'=>'','comment'=>'浏览器'])
                ->addColumn('platform', 'string',['limit'  =>  20,'default'=>'','comment'=>'平台'])
                ->addColumn('login_at', 'integer',['default'=>0,'comment'=>'登录时间'])
                ->addColumn('status', 'integer',['limit' => MysqlAdapter::INT_TINY,'default'=> 1,'comment'=>'1 正常 2 停用'])
                ->create();
        }
    }

    protected function operateLog(): void
    {
        if (!$this->hasTable('log_operate')) {
            $table  =  $this->table('log_operate',['engine'=>'Innodb', 'comment' => '操作日志表', 'signed' => false]);
            $table->addColumn('module', 'string',['limit'  =>  20,'default'=>'','comment'=>'模块'])
                ->addColumn('action', 'string',['limit'  =>  20,'default'=>'','comment'=>'操作'])
                ->addColumn('params', 'string',['limit'  =>  1000,'default'=>'','comment'=>'参数'])
                ->addColumn('ip', 'string',['limit'  =>  20,'default'=>'','comment'=>'IP'])
                ->addColumn('http_method', 'string',['limit'  =>  10,'default'=>'','comment'=>'请求方式'])
                ->addColumn('http_code', 'string',['limit'  =>  255,'default'=>'','comment'=>'http status code'])
                ->addColumn('start_at', 'integer',['default'=>0,'comment'=>'开始时间'])
                ->addColumn('time_token', 'integer',['default'=>0,'comment'=>'耗时'])
                ->addColumn('creator_id', 'integer',['default'=>0,'comment'=>'创建人'])
                ->addColumn('created_at', 'integer',['default'=>0,'comment'=>'创建时间'])
                ->create();
        }
    }
}

<?php

use think\migration\Seeder;

use app\admin\model\Admin;

class Init extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run(): void
    {
        $this->createSuperAdmin();
    }


    protected function createSuperAdmin(): void
    {
        $admin = new Admin();

        $admin->username = 'admin';
        $admin->password = 'catchadmin';
        $admin->email = 'catch@admin.com';
        $admin->creator_id = 1;
        $admin->save();
    }
}

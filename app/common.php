<?php
// 应用公共文件

if (! function_exists('bcrypt')) {
    function bcrypt($value, $algo = PASSWORD_DEFAULT): string
    {
        return password_hash($value, $algo);
    }
}

if (! function_exists('tablePrefix')) {
    function tablePrefix()
    {
        return \config('database.connections.mysql.prefix');
    }
}

if (! function_exists('tableWithoutPrefix')) {
    function tableWithoutPrefix(string $table): string
    {
        return str_replace(tablePrefix(), '', $table);
    }
}



if (! function_exists('importTreeData')) {
    /**
     * import tree data
     *
     * @param array $data
     * @param string $pid
     * @param string $primaryKey
     */
    function importTreeData(array $data, string $pid = 'parent_id', string $primaryKey = 'id'): void
    {
        foreach ($data as $value) {
            if (isset($value[$primaryKey])) {
                unset($value[$primaryKey]);
            }

            $children = $value['children'] ?? false;
            if($children) {
                unset($value['children']);
            }

            $model = new \app\admin\model\permissions\Permissions();

            $menu = $model->where('permission_name', $value['permission_name'])
                ->where('module', $value['module'])
                ->where('permission_mark', $value['permission_mark'])
                ->find();

            if ($menu) {
                $id = $menu->id;
            } else {
                $id = \think\facade\Db::table($model->getTable())->insertGetId($value);
            }
            if ($children) {
                foreach ($children as &$v) {
                    $v[$pid] = $id;
                }

                importTreeData($children, $pid);
            }
        }
    }
}

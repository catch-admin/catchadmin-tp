<?php

namespace app\admin\support\generate\table;

use app\admin\support\Helper;
use app\admin\exceptions\FailedException;
use Phinx\Db\Adapter\AdapterFactory;
use think\facade\Db;
use think\migration\db\Column;

class Table
{
    protected static mixed $adapter = null;

    protected static ?\think\migration\db\Table $table = null;

    protected static ?string $tableName = null;

    public function __construct(string $tableName)
    {
        self::$tableName = $tableName;
    }

    /**
     * @return \think\migration\db\Table
     */
    protected static function getTable(): \think\migration\db\Table
    {
        if (self::$table) {
            return self::$table;
        }

        return (new \think\migration\db\Table(Helper::tableWithoutPrefix(self::$tableName)))->setAdapter(self::getAdapter());
    }

    /**
     * @param string $primaryKey
     * @param string $engine
     * @param string $comment
     * @return bool
     */
    public static function create(string $primaryKey, string $engine, string $comment): bool
    {
        self::getTable()
            ->setId($primaryKey)
            ->setPrimaryKey($primaryKey)
            ->setEngine($engine)
            ->setComment($comment)
            ->setCollation('utf8mb4_general_ci')
            ->create();

        return self::exist();
    }

    /**
     * @return bool
     */
    public static function exist(): bool
    {
        return self::getTable()->exists();
    }

    /**
     * @return bool
     */
    public static function drop(): bool
    {
        if (!self::exist()) {
            throw new FailedException(sprintf('table [%s] not exist, drop failed', self::$tableName));
        }

        self::getTable()->drop();

        return !self::exist();
    }

    /**
     * @param $column
     * @return bool
     */
    public static function addColumn($column): bool
    {
        if ($column instanceof \Closure) {
            $column = $column();
        }

        if (!$column instanceof Column) {
            throw new FailedException('Column Must Be "think\migration\db\Column');
        }

        // 新增字段
        self::getTable()
            ->addColumn($column)
            ->update();

        return self::hasColumn($column->getName());
    }

    /**
     * @param string $column
     * @return bool
     */
    public static function hasColumn(string $column): bool
    {
        return self::getTable()->hasColumn($column);
    }

    /**
     * @return array
     */
    public static function columns(): array
    {
        return array_values(Db::getFields(Helper::tableWithPrefix(self::$tableName)));
    }

    /**
     * @param string $column
     * @return bool
     */
    public static function dropColumn(string $column): bool
    {
        self::getTable()->removeColumn($column)->update();

        if (self::getTable()->hasColumn($column)) {
            throw new FailedException('remove column [' . $column . '] failed');
        }

        return true;
    }

    /**
     * @param $columns
     * @return void
     */
    public static function addUniqueIndex($columns)
    {
        self::getTable()->addIndex($columns, [
            'unique' => true, 'name' => self::$tableName . '_' . (is_string($columns) ? $columns : implode('_', $columns))
        ])->update();
    }

    /**
     * @param $columns
     * @return void
     */
    public static function addIndex($columns)
    {
        self::getTable()->addIndex($columns, [
            'name' => self::$tableName . '_' . (is_string($columns) ? $columns : implode('_', $columns))
        ])->update();
    }

    /**
     * @param $columns
     * @return void
     */
    public static function addFulltextIndex($columns)
    {
        self::getTable()->addIndex($columns, [
            'type' => 'fulltext',
            'name' => self::$tableName . '_' . (is_string($columns) ? $columns : implode('_', $columns))
        ])->update();
    }

    /**
     * @param string $column
     * @return void
     */
    public static function dropIndex(string $column)
    {
        self::getTable()->removeIndex([$column])->update();
    }

    /**
     * @param string $column
     * @return bool
     */
    public static function isIndex(string $column): bool
    {
        return self::getTable()->hasIndex($column);
    }

    /**
     * @return mixed|\Phinx\Db\Adapter\AdapterInterface|\Phinx\Db\Adapter\AdapterWrapper|null
     */
    public static function getAdapter()
    {
        if (self::$adapter) {
            return self::$adapter;
        }

        $options = self::getDbConfig();

        $adapter = AdapterFactory::instance()->getAdapter($options['adapter'], $options);

        if ($adapter->hasOption('table_prefix') || $adapter->hasOption('table_suffix')) {
            $adapter = AdapterFactory::instance()->getWrapper('prefix', $adapter);
        }

        self::$adapter = $adapter;

        return $adapter;
    }

    /**
     * @return array
     */
    protected static function getDbConfig(): array
    {
        $default = app()->config->get('database.default');

        $config = app()->config->get("database.connections.{$default}");

        if (0 == $config['deploy']) {
            $dbConfig = [
                'adapter' => $config['type'],
                'host' => $config['hostname'],
                'name' => $config['database'],
                'user' => $config['username'],
                'pass' => $config['password'],
                'port' => $config['hostport'],
                'charset' => $config['charset'],
                'table_prefix' => $config['prefix'],
            ];
        } else {
            $dbConfig = [
                'adapter' => explode(',', $config['type'])[0],
                'host' => explode(',', $config['hostname'])[0],
                'name' => explode(',', $config['database'])[0],
                'user' => explode(',', $config['username'])[0],
                'pass' => explode(',', $config['password'])[0],
                'port' => explode(',', $config['hostport'])[0],
                'charset' => explode(',', $config['charset'])[0],
                'table_prefix' => explode(',', $config['prefix'])[0],
            ];
        }

        $table = app()->config->get('database.migration_table', 'migrations');

        $dbConfig['migration_table'] = $dbConfig['table_prefix'] . $table;

        return $dbConfig;
    }
}

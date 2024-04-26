<?php
namespace app\admin\support\generate;

use app\admin\support\generate\table\Table;
use app\admin\support\generate\table\TableColumn;
use app\admin\exceptions\FailedException;
use Phinx\Db\Adapter\AdapterInterface;

class Schema extends FileGenerator
{
    protected array $schema;

    public function __construct()
    {
        parent::__construct('Schema');
    }

    public function generate()
    {
        // TODO: Implement generate() method.
        if (!$this->schema['table'] ?? false) {
            throw new FailedException('table name has lost~');
        }

        $this->createTable();

        $this->createTableColumns();

        $this->createTableIndex($this->getIndexColumns());

        return $this->schema['table'];
    }

    /**
     * 创建表
     *
     * @return void
     */
    protected function createTable(): void
    {
        $table = new Table($this->schema['table']);

        if ($table::exist()) {
            throw new FailedException(sprintf('表 [%s] 已存在', $this->schema['table']));
        }

        if(!$table::create(
            $this->schema['primary'],
            $this->schema['engine'],
            $this->schema['comment']
        )) {
            throw new FailedException(sprintf('创建表 [%s] 失败，请检查', $this->schema['table']));
        }
    }

    /**
     * 创建 columns
     *
     * @return void
     */
    protected function createTableColumns(): void
    {
        $tableColumns = [];

        foreach ($this->schema['fields'] as $column) {
            if ($column['type'] === AdapterInterface::PHINX_TYPE_DECIMAL) {
                $tableColumn = (new TableColumn)->{$column['type']}($column['name']);
            } else if ($column['type'] === AdapterInterface::PHINX_TYPE_ENUM || $column['type'] === AdapterInterface::PHINX_TYPE_SET) {
                $tableColumn = (new TableColumn)->{$column['type']}($column['name'], $column['default']);
            }else {
                $tableColumn = (new TableColumn)->{$column['type']}($column['name'], $column['length'] ?? 0);
            }

            if ($column['nullable']) {
                $tableColumn->setNullable();
            }

            if ($column['unsigned']) {
                $tableColumn->setUnsigned();
            }

            if ($column['comment']) {
                $tableColumn->setComment($column['comment']);
            }

            if (! $this->doNotNeedDefaultValueType($column['type'])) {
                $tableColumn->setDefault($column['nullable'] ? null : $column['default']);
            }

            $tableColumns[] = $tableColumn;
        }

        if ($this->schema['creatorId']) {
            $tableColumns[] = $this->createCreatorIdColumn();
        }

        if ($this->schema['createdAt']) {
            $tableColumns[] = $this->createCreateAtColumn();
            $tableColumns[] = $this->createUpdateAtColumn();
        }

        if ($this->schema['deletedAt']) {
            $tableColumns[] = $this->createDeleteAtColumn();
        }

        foreach ($tableColumns as $column) {
            if (!Table::addColumn($column)) {
                throw new FailedException('创建失败');
            }
        }
    }

    /**
     * 创建 index
     *
     * @param $indexes
     * @return void
     */
    protected function createTableIndex($indexes): void
    {
        $method = [
            'index' => 'addIndex',
            'unique' => 'addUniqueIndex',
            'fulltext' => 'addFulltextIndex',
        ];

        foreach ($indexes as $type => $index) {
            foreach ($index as $i) {
                Table::{$method[$type]}($i);
            }
        }
    }

    /**
     * 获取有索引的 column
     *
     * @return array
     */
    protected function getIndexColumns(): array
    {
        $index = [];

        foreach ($this->schema['fields'] as $column) {
            if ($column['index']) {
                $index[$column['index']][] = $column['name'];
            }
        }

        return $index;
    }

    /**
     * 不需要默认值
     *
     * @param string $type
     * @return  bool
     */
    protected function doNotNeedDefaultValueType(string $type): bool
    {
        return in_array($type, [
            'blob', 'text', 'geometry', 'json',
            'tinytext', 'mediumtext', 'longtext',
            'tinyblob', 'mediumblob', 'longblob', 'enum', 'set',
            'date', 'datetime', 'time', 'timestamp', 'year'
        ]);
    }


    /**
     * 创建时间
     *
     * @return \think\migration\db\Column
     */
    protected function createCreateAtColumn(): \think\migration\db\Column
    {
        return (new TableColumn)->int('created_at', 10)
            ->setUnsigned()
            ->setDefault(0)
            ->setComment('创建时间');
    }

    /**
     * 更新时间
     *
     * @return \think\migration\db\Column
     */
    protected function createUpdateAtColumn(): \think\migration\db\Column
    {
        return (new TableColumn)->int('updated_at', 10)
            ->setUnsigned()->setDefault(0)->setComment('更新时间');
    }

    /**
     * 软删除
     *
     * @return \think\migration\db\Column
     */
    protected function createDeleteAtColumn(): \think\migration\db\Column
    {
        return (new TableColumn)->int('deleted_at', 10)
            ->setUnsigned()->setDefault(0)->setComment('软删除字段');
    }

    /**
     * 创建人
     *
     * @return \think\migration\db\Column
     */
    protected function createCreatorIdColumn(): \think\migration\db\Column
    {
        return (new TableColumn)->int('creator_id', 10)
            ->setUnsigned()->setDefault(0)->setComment('创建人ID');
    }


    public function setSchema(array $schema): static
    {
        $this->schema = $schema;

        return $this;
    }
}

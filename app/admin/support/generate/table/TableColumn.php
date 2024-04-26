<?php

namespace app\admin\support\generate\table;

use Phinx\Db\Adapter\AdapterInterface;
use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\db\Column;

class TableColumn
{
    /**
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function tinyint(string $name, int $length): Column
    {
        return Column::tinyInteger($name);
    }

    /**
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function boolean(string $name, int $length): Column
    {
        return Column::boolean($name);
    }

    /**
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function smallint(string $name, int $length): Column
    {
        return Column::smallInteger($name);
    }

    /**
     * int
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function int(string $name, int $length): Column
    {
        return Column::integer($name);
    }

    /**
     * mediumint
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function mediumint(string $name, int $length): Column
    {
        return Column::mediumInteger($name);
    }

    /**
     * bigint
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function bigint(string $name, int $length): Column
    {
        return Column::bigInteger($name);
    }

    /**
     * 浮点数
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function float(string $name, int $length): Column
    {
        return Column::float($name);
    }

    /**
     * 浮点数
     *
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return Column
     */
    public function decimal(string $name, $precision = 8, $scale = 2): Column
    {
        return Column::decimal($name, $precision, $scale);
    }

    /**
     * string 类型
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function varchar(string $name, int $length): Column
    {
        return Column::string($name, $length);
    }

    /**
     * char
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function char(string $name, int $length): Column
    {
        return Column::char($name, $length);
    }

    /**
     * 普通文本
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function text(string $name, int $length): Column
    {
        return Column::text($name);
    }

    /**
     * 小文本
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function tinytext(string $name, int $length): Column
    {
        return Column::make($name, AdapterInterface::PHINX_TYPE_TEXT, ['length' => MysqlAdapter::TEXT_TINY]);
    }

    /**
     * 中长文本
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function mediumtext(string $name, int $length): Column
    {
        return Column::mediumText($name);
    }

    /**
     * 超大文本
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function longtext(string $name, int $length): Column
    {
        return Column::longText($name);
    }

    /**
     * binary
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function binary(string $name, int $length): Column
    {
        return Column::binary($name);
    }

    /**
     * varbinary
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function varbinary(string $name, int $length): Column
    {
        return Column::make($name, AdapterInterface::PHINX_TYPE_VARBINARY);
    }

    /**
     * tinyblob
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function tinyblob(string $name, int $length): Column
    {
        return Column::make($name, AdapterInterface::PHINX_TYPE_BLOB, ['length' => MysqlAdapter::BLOB_TINY]);
    }

    /**
     * blob
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function blob(string $name, int $length): Column
    {
        return Column::make($name, AdapterInterface::PHINX_TYPE_BLOB, ['length' => MysqlAdapter::BLOB_REGULAR]);
    }

    /**
     * mediumblob
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function mediumblob(string $name, int $length): Column
    {
        return Column::make($name, AdapterInterface::PHINX_TYPE_BLOB, ['length' => MysqlAdapter::BLOB_MEDIUM]);
    }

    /**
     * longblob
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function longblob(string $name, int $length): Column
    {
        return Column::make($name, AdapterInterface::PHINX_TYPE_BLOB, ['length' => MysqlAdapter::BLOB_LONG]);
    }

    /**
     * 时间类型
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function date(string $name, int $length): Column
    {
        return Column::date($name);
    }

    /**
     * 日期时间
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function datetime(string $name, int $length): Column
    {
        return Column::dateTime($name)->setOptions(['default' => 'CURRENT_TIMESTAMP']);
    }

    /**
     * 实践格式
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function time(string $name, int $length): Column
    {
        return Column::time($name);
    }

    /**
     * 时间戳
     *
     * @param string $name
     * @param int $length
     * @return Column
     */
    public function timestamp(string $name, int $length): Column
    {
        return Column::timestamp($name)->setOptions(['default' => 'CURRENT_TIMESTAMP']);
    }

    /**
     * enum 类型
     *
     * @param string $name
     * @param array $values
     * @return Column
     */
    public function enum(string $name, array $values): Column
    {
        return Column::enum($name, $values);
    }

    /**
     * set 类型
     *
     * @param string $name
     * @param array $values
     * @return Column
     */
    public function set(string $name, array $values): Column
    {
        return Column::make($name, AdapterInterface::PHINX_TYPE_SET, compact('values'));
    }


    /**
     * json 穿
     *
     * @param string $name
     * @return Column
     */
    public function json(string $name): Column
    {
        return Column::json($name);
    }

    /**
     * uuid
     *
     * @param string $name
     * @return Column
     */
    public function uuid(string $name): Column
    {
        return Column::uuid($name);
    }
}

<?php

declare(strict_types=1);

namespace app\admin\support;

use think\facade\Cache;
use think\model\Collection;

class CatchModelCollection extends Collection
{
    /**
     * tree 结构
     *
     * @time 2020年10月21日
     * @param int $pid
     * @param string $pidField
     * @param string $children
     * @return array
     */
    public function toTree(int $pid = 0, string $pidField = 'parent_id', string $children = 'children'): array
    {
        $pk = 'id';

        if ($this->count()) {
            $pk = $this->first()->getPk();
        }

        return Tree::setPk($pk)->done($this->toArray(), $pid, $pidField, $children);
    }


    /**
     * @param $key
     * @param int $ttl
     * @param string $store
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cache($key, int $ttl = 0, string $store = 'redis'): bool
    {
        return Cache::store($store)->set($key, $this->items, $ttl);
    }

    /**
     * 获取当前级别下的所有子级
     *
     * @time 2020年11月04日
     * @param array $ids
     * @param string $parentFields
     * @param string $column
     * @return array
     */
    public function getAllChildrenIds(array $ids, string $parentFields = 'parent_id', string $column = 'id'): array
    {
        array_walk($ids, function (&$item) {
            $item = intval($item);
        });

        $childIds = $this->whereIn($parentFields, $ids)->column($column);

        if (!empty($childIds)) {
            $childIds = array_merge($childIds, $this->getAllChildrenIds($childIds));
        }

        return $childIds;
    }

    /**
     * implode
     *
     * @time 2021年02月24日
     * @param string $separator
     * @param string $column
     * @return string
     */
    public function implode(string $column = '', string $separator = ','): string
    {
        return implode($separator, $column ? array_column($this->items, $column) : $this->items);
    }
}

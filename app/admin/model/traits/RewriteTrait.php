<?php
namespace app\admin\model\traits;

use app\admin\support\CatchModelCollection;
use think\Collection;

/**
 * 重写 think\Model 的方法
 *
 * Trait RewriteTrait
 * @package catcher\traits\db
 */
trait RewriteTrait
{
    /**
     * @return array
     */
    protected function defaultHiddenFields(): array
    {
        return [$this->deleteTime];
    }

    /**
     * @param array $hidden
     * @param bool $merge
     * @return $this
     */
    public function hidden(array $hidden = [], bool $merge = false): static
    {
        /**
         * 合并属性
         */
        if (!count($this->hidden)) {
            $this->hidden = array_merge($this->hidden, $hidden);

            return $this;
        }

        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @param iterable $collection
     * @param string|null $resultSetType
     * @return Collection
     */
    public function toCollection(iterable $collection = [], string $resultSetType = null): Collection
    {
        $resultSetType = $resultSetType ?: $this->resultSetType;

        if ($resultSetType && str_contains($resultSetType, '\\')) {
            $collection = new $resultSetType($collection);
        } else {
            $collection = new CatchModelCollection($collection);
        }

        return $collection;
    }
}

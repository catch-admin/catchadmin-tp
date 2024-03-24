<?php
declare(strict_types=1);

namespace app\admin\controller\traits;

use app\admin\model\Admin;

trait ScopeTrait
{

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCreator($query): mixed
    {
        if (property_exists($this, 'schema') && in_array('creator_id', $this->schema)) {
            return $query->addSelectSub(function () {
                $user = app(Admin::class);
                return $user->whereColumn($this->getTable() . '.creator_id', $user->getTable() . '.id')
                    ->field('username');
            }, 'creator');
        }

        return $query;
    }
}

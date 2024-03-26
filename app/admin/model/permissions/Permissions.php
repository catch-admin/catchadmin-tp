<?php

namespace app\admin\model\permissions;

use app\admin\commands\Model;
use app\admin\model\CatchModel;
use think\model\relation\HasMany;

class Permissions extends CatchModel
{
	/**
	 * 可写入的字段
	 * @var string[]
	 */
	protected $field = [
		'id',
		'permission_name',
		'parent_id',
		'route',
		'icon',
		'module',
		'permission_mark',
		'type',
		'active_menu',
		'component',
		'redirect',
		'hidden',
		'keepalive',
		'creator_id',
		'sort',
		'created_at',
		'updated_at',
		'deleted_at',
	];

    const HIDDEN = 2;
    const SHOW = 1;

    public function isHidden(): bool
    {
        return $this->hidden == self::SHOW;
    }

    protected bool $asTree = true;

    protected bool $isPaginate = false;

    const TOP_MENU = 1;
    const MENU = 2;
    const ACTION_MENU=3;


     /**
      * actions
      *
      * @return HasMany
      */
    public function actions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->where('type', self::ACTION_MENU);
    }

    /**
     * default permission actions
     *
     * @var array|string[]
     */
    protected array $defaultActions = [
        'index' => '列表',
        'save' => '新增',
        'read' => '读取',
        'update' => '更新',
        'delete' => '删除',
        'enable' => '禁用/启用',
        'import' => '导入',
        'export' => '导出',
    ];


    /**
     * action type
     *
     * @return bool
     */
    public function isAction(): bool
    {
        return $this->type == self::ACTION_MENU;
    }

    /**
     * is top menu
     *
     * @return bool
     */
    public function isTopMenu(): bool
    {
        return $this->type == self::TOP_MENU;
    }

    /**
     * is menu
     *
     * @return bool
     */
    public function isMenu(): bool
    {
        return $this->type == self::MENU;
    }


    /**
     *
     * @param array $data
     * @return mixed
     */
    public function storeBy(array $data): mixed
    {
        return $this->transaction(function () use ($data){
            if ($data['actions'] ?? false) {
                /* @var static $parentMenu */
                $parentMenu =  $this->firstBy(value: $data['parent_id'], field: 'id');

                if (! $parentMenu->isMenu()) {
                    return false;
                }

                // 控制器命名空间
                $controllerNamespace = 'app\\admin\\controller\\';
                $controller = $controllerNamespace . $parentMenu->module . '\\' . $data['permission_mark'];
                $reflectClass = new \ReflectionClass($controller);
                $actions = [];
                foreach ($reflectClass->getMethods() as $method) {
                    if ($method->isPublic() && ! $method->isConstructor()) {
                        $actions[] = $method->getName();
                    }
                }
                foreach ($actions as $k => $action) {
                    if (! isset($this->defaultActions[$action])) {
                        continue;
                    }

                    $this->addAction($this->newInstance([
                        'type' => self::ACTION_MENU,
                        'parent_id' => $data['parent_id'],
                        'permission_name' => $this->defaultActions[$action],
                        'permission_mark' => $action,
                        'sort' => $k + 1
                    ]), $parentMenu);
                }

                return true;
            }

            $model = $this->data($data);

            if ($model->isAction()) {
                $parentMenu = $this->firstBy($model->parent_id, 'id');
                return $this->addAction($model, $parentMenu);
            }

            if ($model->isTopMenu()) {
                $data['route'] = '/'.trim($data['route'], '/');
            }

            if (isset($data['component'])) {
                $data['component'] = str_replace('\\', '/', $data['component']);
            }
            return parent::storeBy($data);
        });
    }

    /**
     * add action
     *
     * @param CatchModel|Model $model
     * @param Permissions $parent
     * @return mixed
     */
    protected function addAction(CatchModel|Model $model, mixed $parent): mixed
    {
        $model->setAttr('module', $parent->module);
        $model->setAttr('permission_mark', $parent->permission_mark. '@'.  $model->permission_mark);
        $model->setAttr('route', '');
        $model->setAttr('icon', '');
        $model->setAttr('component', '');
        $model->setAttr('redirect', '');

        if ($this->where('module', $model->getAttr('module'))->where('permission_mark', $model->getAttr('permission_mark'))->find()) {
            return false;
        }

        return $model->setCreatorId()->save();
    }


    /**
     * update data
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function updateBy($id, array $data): mixed
    {
        $model = $this->data($data);

        if ($model->isAction()) {
            /* @var Permissions $parentMenu */
            $parentMenu = $this->firstBy($model->parent_id, 'id');
            $data['permission_mark'] = $parentMenu->permission_mark.'@'.$data['permission_mark'];
        }

        if (isset($data['component'])) {
            $data['component'] = str_replace('\\', '/', $data['component']);
        }

        return parent::updateBy($id, $data);
    }
}

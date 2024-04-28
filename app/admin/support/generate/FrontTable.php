<?php

// +----------------------------------------------------------------------
// | CatchAdmin [Just Like ～ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2021 https://catchadmin.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://github.com/JaguarJack/catchadmin-laravel/blob/master/LICENSE.md )
// +----------------------------------------------------------------------
// | Author: JaguarJack [ njphper@gmail.com ]
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\admin\support\generate;

use think\File;
use think\helper\Str;

class FrontTable extends FileGenerator
{
    /**
     * @var string
     */
    protected string $label = '{label}';

    /**
     * @var string
     */
    protected string $prop = '{prop}';

    /**
     * @var string
     */
    protected string $modelValue = '{model-value}';

    /**
     * @var string
     */
    protected string $table = '{table}';

    /**
     * @var string
     */
    protected string $search = '{search}';

    /**
     * @var string
     */
    protected string $api = '{api}';

    /**
     * @var string
     */
    protected string $formItems = '{formItems}';

    /**
     * @var string
     */
    protected string $paginate = '{paginate}';

    /**
     * @var string
     */
    protected string $useList = '{useList}';

    /**
     * @var string
     */
    protected string $tree = '{tree}';

    /**
     * @var array
     */
    protected array $structures;

    /**
     * @param string $controller
     * @param bool $hasPaginate
     * @param string $apiString
     */
    public function __construct(
        protected string $controller,
        protected bool $hasPaginate,
        protected string $apiString
    ) {
        parent::__construct($controller, '');

    }

    /**
     * get content
     *
     * @return string
     */
    public function getContent(): string
    {
        // TODO: Implement getContent() method.
        return str_replace([
            $this->table, $this->search, $this->api, $this->paginate, $this->useList, $this->tree
        ], [
            $this->getTableContent(),
            $this->getSearchContent(),
            $this->apiString,
            $this->getPaginateStubContent(),
            $this->getUseList(),
            $this->getTreeProps()
        ], file_get_contents($this->getTableStub()));
    }

    /**
     * get file
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->getViewPath() . DIRECTORY_SEPARATOR . 'index.vue';
    }


    /**
     * get search content
     *
     * @return string
     */
    protected function getSearchContent(): string
    {
        $search = '';

        $formComponents = $this->formComponents();

        foreach ($this->structures as $structure) {
            if ($structure['comment'] && $structure['component'] && $structure['op']) {
                if (isset($formComponents[$structure['component']])) {
                    $search .= str_replace(
                        [$this->label, $this->prop, $this->modelValue],
                        [$this->parseComment($structure['label']), $structure['name'], sprintf('query.%s', $structure['name'])],
                        $formComponents[$structure['component']]
                    ) . PHP_EOL;
                }
            }
        }

        return trim($search, PHP_EOL);
    }

    /**
     * get list content;
     *
     * @return string
     */
    protected function getTableContent(): string
    {
        $tableColumn = <<<HTML
<el-table-column prop="{prop}" label="{label}" />
HTML;

        $table = '';

        foreach ($this->structures as $structure) {
            if ($structure['name'] && $structure['comment']) {
                $table = str_replace([$this->label, $this->prop], [$this->parseComment($structure['comment']), $structure['name']], $tableColumn) . PHP_EOL;
            }
        }

        return trim($table, PHP_EOL);
    }

    /**
     * get formItem stub
     *
     * @return string
     */
    protected function getFormItemStub(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'stubs'

            .DIRECTORY_SEPARATOR.'vue'.DIRECTORY_SEPARATOR

            .'formItems'.DIRECTORY_SEPARATOR.'*.stub';
    }


    /**
     * get table stub
     *
     * @return string
     */
    protected function getTableStub(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'stubs'

            .DIRECTORY_SEPARATOR.'vue'.DIRECTORY_SEPARATOR.'table.stub';
    }

    /**
     * get paginate stub content
     *
     * @return string
     */
    protected function getPaginateStubContent(): string
    {
        return $this->hasPaginate ? '<Paginate />' : '';
    }

    /**
     * get use List
     * @return string
     */
    protected function getUseList(): string
    {
        if ($this->hasPaginate) {
            return 'const { data, query, search, reset, loading } = useGetList(api)';
        } else {
            return 'const { data, query, search, reset, loading } = useGetList(api, false)';
        }
    }

    /**
     * get tree props
     *
     * @return string
     */
    public function getTreeProps(): string
    {
        if (in_array('parent_id', array_column($this->structures, 'name'))) {
            return ' row-key="id" default-expand-all :tree-props="{ children: \'children\' }"';
        }

        return ' ';
    }

    /**
     * set structures
     *
     * @param array $structures
     * @return $this
     */
    public function setStructures(array $structures): static
    {
        $this->structures = $structures;

        return $this;
    }

    public function generate(): string
    {
        // TODO: Implement generate() method.
        file_put_contents($this->getFile(), $this->getContent());

        return $this->getFile();
    }
}

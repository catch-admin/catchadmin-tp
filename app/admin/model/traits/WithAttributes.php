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

namespace app\admin\model\traits;

/**
 * base operate
 */
trait WithAttributes
{
    /**
     * @var string
     */
    protected string $parentIdColumn = 'parent_id';

    /**
     * @var string
     */
    protected string $sortField = '';

    /**
     * @var bool
     */
    protected bool $sortDesc = true;

    /**
     * as tress which is show in list as tree data
     *
     * @var bool
     */
    protected bool $asTree = false;

    /**
     * columns which show in list
     *
     * @var array
     */
    protected array $fields = ['*'];


    /**
     * @var bool
     */
    protected bool $isPaginate = true;

    /**
     * @var bool
     */
    protected bool $dataRange = false;

    /**
     * 快速搜索
     *
     * @var array
     */
    public array $searchable = [];


    /**
     * 表单提交自动写入关联
     *
     * @var array
     */
    protected array $autoWriteRelations = [];

    /**
     * null to empty string
     *
     * @var bool
     */
    protected bool $autoNull2EmptyString = true;
}

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


use think\facade\Log;

class FrontForm extends FileGenerator
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
     * @var array
     */
    protected array $structures;

    /**
     * @param string $controller
     */
    public function __construct(string $controller)
    {
        parent::__construct($controller, '');
    }

    /**
     * get content
     *
     * @return string
     */
    public function getContent(): string
    {
        return str_replace($this->formItems, $this->getFormContent(), file_get_contents($this->getFormStub()));
        // TODO: Implement getContent() method.
        // return Str::of(File::get($this->getFormStub()))->replace($this->formItems, $this->getFormContent())->toString();
    }

    /**
     * get file
     *
     * @return string
     */
    public function getFile(): string
    {
        // TODO: Implement getFile() method.
        return $this->getViewPath().DIRECTORY_SEPARATOR.'create.vue';
    }

    /**
     * get form content
     *
     * @return string
     */
    protected function getFormContent(): string
    {
        $form = '';

        $formComponents = $this->formComponents();

        foreach ($this->structures as $structure) {
            if ($structure['comment'] && $structure['component']) {
                if (isset($formComponents[$structure['component']])) {
                    $form .= str_replace(
                            [$this->label, $this->prop, $this->modelValue],
                            [$this->parseComment($structure['comment']), $structure['name'], sprintf('formData.%s', $structure['name'])],
                            $formComponents[$structure['component']]
                        ) . PHP_EOL;
                }
            }
        }

        return trim($form, PHP_EOL);
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
     * get form stub
     *
     * @return string
     */
    public function getFormStub(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'stubs'

            .DIRECTORY_SEPARATOR.'vue'.DIRECTORY_SEPARATOR.'form.stub';
    }

    /**
     * set structures
     *
     * @param $structures
     * @return $this
     */
    public function setStructures($structures): static
    {
        $this->structures = $structures;

        return $this;
    }

    public function generate()
    {
        // TODO: Implement generate() method.
        file_put_contents($this->getFile(), $this->getContent());

        return $this->getFile();
    }
}

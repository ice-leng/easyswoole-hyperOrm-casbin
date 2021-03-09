<?php

namespace EasySwooleTool\HyperfOrm\Permission\Annotation;

use EasySwoole\Annotation\AbstractAnnotationTag;
use EasySwoole\HttpAnnotation\Exception\Annotation\InvalidTag;

/**
 * Class ApiMenu
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiMenu extends AbstractAnnotationTag
{
    /**
     * @var string 模块组  必填
     */
    public $modules;

    /**
     * 模块名称， 如果为空 默认为 注解 ApiGroup.groupName
     */
    public $module;

    public function tagName(): string
    {
        return 'ApiMenu';
    }

    function __onParser()
    {
        if(empty($this->groupName)){
            throw new InvalidTag("modules for ApiMenu tag is require");
        }
    }
}

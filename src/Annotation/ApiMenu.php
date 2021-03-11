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
     * @var int 排序
     */
    public $sort;

    /**
     * 模块名称， 如果为空 默认为 注解 ApiGroup.groupName  如果分组 格式为 xx/xx/xx
     */
    public $name;

    /**
     * 验证权限 path
     */
    public $check;

    /**
     * 给前端的， 如果没有 就为 check 数据
     */
    public $id;

    public function tagName(): string
    {
        return 'ApiMenu';
    }

    function __onParser()
    {
        if(empty($this->sort)){
            throw new InvalidTag("sort for ApiMenu tag is require");
        }
    }
}

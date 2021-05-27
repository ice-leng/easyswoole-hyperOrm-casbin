<?php

namespace EasySwoole\HyperfOrm\Permission\Annotation;

use EasySwoole\Annotation\AbstractAnnotationTag;

/**
 * Class ApiPermission
 * @package EasySwoole\HttpAnnotation\AnnotationTag
 * @Annotation
 */
class ApiPermission extends AbstractAnnotationTag
{

    /**
     * 资源， 默认为  注解Api  的 path
     */
    public $path;

    /**
     * 请求方式 默认为 注解 Method.allow, 如果都没有定义，则 全部
     */
    public $method;

    /**
     * 名称 默认为 注解Api 的 name
     */
    public $name;

    /**
     * 是否菜单项
     * @var bool
     */
    public $isMenu = false;

    /**
     * 默认是否自动加入权限列表显示
     */
    public $display = true;

    public function tagName(): string
    {
        return 'ApiPermission';
    }
}

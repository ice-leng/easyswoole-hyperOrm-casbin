<?php

namespace EasySwoole\HyperfOrm\Permission\Annotation;

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
     * 给前端的， 如果没有 就为 path
     */
    public $key;

    /**
     * 路由， 默认为 list 的 path
     */
    public $path;

    /**
     * @var string
     */
    public $icon = '';

    /**
     * 模块名称 如果没有 默认为 ApiGroupDescription
     */
    public $name;

    /**
     * 是否严格匹配路由
     * @var bool
     */
    public $exact = true;

    /**
     * 重定向到某个路由地址
     * @var string
     */
    public $redirect = '';

    /**
     * 页面组件路径，相对位置
     *
     * @var string
     */
    public $componentPath = '@/pages/Default/index';

    /**
     * 是否菜单项
     * @var bool
     */
    public $isMenu = true;

    /**
     * 请求方式
     */
    public $method = 'POST';

    /**
     * @var int 排序
     */
    public $sort;

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

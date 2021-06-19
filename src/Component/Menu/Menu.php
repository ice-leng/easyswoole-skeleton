<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/18
 * Time:  5:56 下午
 */

namespace EasySwoole\Skeleton\Component\Menu;

use EasySwoole\Skeleton\BaseObject;

class Menu extends BaseObject
{
    /**
     * 给前端的
     */
    public $key;

    /**
     * 路由，
     */
    public $path;

    /**
     * @var string
     */
    public $icon = '';

    /**
     * @var string
     */
    public $parentIcon = '';

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

    /**
     * 角色
     * @var array
     */
    public $role = [];
}

<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/18
 * Time:  6:08 下午
 */

namespace EasySwoole\Skeleton\Component\Menu;

use EasySwoole\Utility\SnowFlake;

class MenuService
{
    /**
     * @param array $array
     * @param       $path
     * @param       $value
     */
    protected function setValue(array &$array, $path, $value): void
    {
        if ($path === null) {
            $array = $value;
            return;
        }

        $keys = is_array($path) ? $path : explode('/', $path);

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            if (!is_array($array[$key])) {
                $array[$key] = [$array[$key]];
            }
            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    /**
     * @param      $array
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    protected function getValue($array, $key, $default = null)
    {
        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '/')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        }

        if (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        }

        return $default;
    }

    /**
     * @param string $name
     * @param string $icon
     *
     * @return array
     */
    protected function initMenu(string $name, string $icon): array
    {
        $apiMenu = new Menu();
        $apiMenu->name = $name;
        $apiMenu->icon = $icon;
        $apiMenu->key = '/' . SnowFlake::make(1, 1);
        $apiMenu->path = $apiMenu->key;
        $fileds = [
            'key',
            'path',
            'icon',
            'name',
            'exact',
            'redirect',
            'componentPath',
            'isMenu',
            'method',
        ];
        $data = [];
        foreach ($fileds as $filed) {
            $data[$filed] = $apiMenu->{$filed};
        }
        return $data;
    }

    /**
     * @param array $permission
     *
     * @return array[]
     */
    protected function getMenuForConfig(array $permission = []): array
    {
        $menuList = $parentMenuIcon = [];
        $results = config('menu', []);
        foreach ($results as $result) {
            $menu = new Menu($result);
            $sort = $menu->sort;
            $name = $menu->name;
            $router = $menu->path;
            if ($menu->isMenu) {
                $menu->isMenu = in_array($router, $permission);
            }
            $names = explode('/', $name);
            $menuName = array_pop($names);
            $newName = implode('/', $names);
            if (empty($parentMenuIcon[$newName])) {
                $parentMenuIcon[$newName] = $menu->parentIcon;
            }
            $menuList[$sort][$name] = [
                'key'           => $menu->key ?? $router,
                'path'          => $router,
                'icon'          => $menu->icon,
                'name'          => $menuName,
                'exact'         => $menu->exact,
                'redirect'      => $menu->redirect,
                'componentPath' => $menu->componentPath,
                'isMenu'        => $menu->isMenu,
                'method'        => $menu->method,
            ];
        }
        return [$menuList, $parentMenuIcon];
    }

    /**
     * @param array $permission
     *
     * @return array
     */
    public function generateMenu(array $permission = []): array
    {
        [$menus, $parentMenuIcon] = $this->getMenuForConfig($permission);
        $results = $data = [];
        foreach ($menus as $menu) {
            foreach ($menu as $key => $value) {
                $hasMenu = $this->getValue($results, $key, []);
                $newMenu = array_merge($hasMenu, $value);
                $this->setValue($results, $key, $newMenu);
            }
        }
        foreach ($results as $name => $result) {
            $parentMenu = $this->initMenu($name, $parentMenuIcon[$name] ?? '');
            $parentMenu['childRoutes'] = count($result) === 1 ? $this->generateChildMenu($result, $parentMenuIcon, $name) : array_values($result);
            $data[] = $parentMenu;
        }
        return $data;
    }

    /**
     * @param array  $menus
     * @param array  $parentMenuIcon
     * @param string $parentName
     *
     * @return array
     */
    protected function generateChildMenu(array $menus, array $parentMenuIcon, string $parentName)
    {
        $data = [];
        foreach ($menus as $name => $menu) {
            $parentName .= ('/' . $name);
            $parentMenu = $this->initMenu($name, $parentMenuIcon[$parentName] ?? '');
            $parentMenu['childRoutes'] = count($menu) === 1 ? $this->generateChildMenu($menu, $parentMenuIcon, $parentName) : array_values($menu);
            $data[] = $parentMenu;
        }
        return $data;
    }
}

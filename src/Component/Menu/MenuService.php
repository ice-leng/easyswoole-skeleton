<?php
/**
 * Created by PhpStorm.
 * User:  ice
 * Email: xykxyk2008@163.com
 * Date:  2021/6/18
 * Time:  6:08 下午
 */

namespace EasySwoole\Skeleton\Component\Menu;

use EasySwoole\Skeleton\Entities\PageEntity;
use EasySwoole\Skeleton\Errors\CommonError;
use EasySwoole\Skeleton\Framework\BaseService;
use EasySwoole\Skeleton\Framework\BizException;
use EasySwoole\Skeleton\Utility\InitializeUtil;
use EasySwoole\Utility\File;
use EasySwoole\Utility\FileSystem;
use EasySwoole\Utility\SnowFlake;

class MenuService extends BaseService
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
     * @param string $key
     *
     * @return array[]
     */
    protected function getMenuForConfig(string $key): array
    {
        $menuList = $parentMenuIcon = [];
        $results = config('menu', []);
        $roleInterface = make(RoleInterface::class);
        $roles = $roleInterface ? $roleInterface->getRoleByKey($key) : [];
        $permissionInterface = make(PermissionInterface::class);
        $permissions = $permissionInterface ? $permissionInterface->getPermissionByKey($key) : [];
        $isSupper = (empty($roles) && empty($permissions)) || in_array(-1, $roles) || in_array(-1, $permissions);
        foreach ($results as $result) {
            $menu = new Menu($result);
            if (!$isSupper || empty(array_intersect($menu->role, $roles)) || !in_array($menu->path, $permissions)) {
                continue;
            }
            $sort = $menu->sort;
            $name = $menu->name;
            $router = $menu->path;
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
     * @param string $key
     *
     * @return array
     */
    public function generateMenu(string $key): array
    {
        [$menus, $parentMenuIcon] = $this->getMenuForConfig($key);
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

    /**
     * @param array           $params
     * @param array|string[]  $field
     * @param PageEntity|null $pageEntity
     *
     * @return array
     */
    public function getList(array $params = [], array $field = ['*'], ?PageEntity $pageEntity = null): array
    {
        $results = config('menu', []);
        return $this->pageByArray($results, $pageEntity);
    }

    public function create(array $params): array
    {
        $menu =  (new Menu($params))->toArray();
        $results = config('menu', []);
        $results[] = $menu;
        $this->put($results);
        return $menu;
    }

    public function update(array $params): array
    {
        $menu =  (new Menu($params))->toArray();
        $results = config('menu', []);
        $isUpdate = false;
        foreach ($results as $key => $item) {
            if ($item['path'] === $menu['path']) {
                $results[$key] = $menu;
                $isUpdate = true;
                break;
            }
        }
        if (!$isUpdate) {
            throw new BizException(CommonError::INVALID_PARAMS);
        }
        $this->put($results);
        return $menu;
    }

    public function remove(array $params): int
    {
        $menu =  (new Menu($params))->toArray();
        $results = config('menu', []);
        $isRemove = false;
        foreach ($results as $key => $item) {
            if ($item['path'] === $menu['path']) {
                unset($results[$key]);
                $isRemove = true;
                break;
            }
        }
        if (!$isRemove) {
            throw new BizException(CommonError::INVALID_PARAMS);
        }
        $results = array_values($results);
        $this->put($results);
        return 1;
    }

    protected function put(array $results)
    {
        $fileSystem = new FileSystem();
        $file = EASYSWOOLE_ROOT . '/App/Configs/menu.php';
        if ($fileSystem->missing($file)) {
            File::touchFile($file);
        }
        $fileSystem->put($file, "<?php\n\nreturn " . var_export($results, true) . ";\n");
        InitializeUtil::config();
    }
}

<?php

namespace EasySwooleTool\HyperfOrm\Permission;

use Casbin\Enforcer;
use EasySwoole\Component\Singleton;
use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Utility\Scanner;
use EasySwooleTool\HyperfOrm\Permission\Annotation\ApiMenu;
use EasySwooleTool\HyperfOrm\Permission\Annotation\ApiPermission;
use Casbin\Exceptions\CasbinException;

class Permission
{
    use Singleton;

    /**
     * @var Enforcer
     */
    protected $enforcer;

    protected $scanPermissionList = [];

    /**
     * @param string $path
     *
     * @return array
     */
    public function scanPermission(string $path): array
    {
        if (!empty($this->scanPermissionList[$path])) {
            return $this->scanPermissionList[$path];
        }
        $menuList = $permissionList = $data = [];
        $apiMenu = new ApiMenu();
        $apiPermission = new ApiPermission();
        $list = (new Scanner())->scanAnnotations($path);
        /**
         * @var ObjectAnnotation $objectAnnotation
         */
        foreach ($list as $objectAnnotation) {
            $menus = $objectAnnotation->getOtherTags()[$apiMenu->tagName()] ?? [];
            if (empty($menus[0])) {
                continue;
            }
            $menu = $menus[0];
            $sort = $menu->sort;
            $name = $menu->name;
            $menuList[$sort][$name] = [
                'name'   => $name,
                'check'  => $menu->check,
                'method' => $menu->method,
                'id'     => $menu->id ?? $menu->check,
            ];
            if (is_null($name)) {
                $name = '默认';
                if ($objectAnnotation->getApiGroupTag()) {
                    $name = $objectAnnotation->getApiGroupTag()->groupName;
                }
            }
            $annotationPermission = [];
            /**
             * @var                  $methodName
             * @var MethodAnnotation $method
             */
            foreach ($objectAnnotation->getMethod() as $methodName => $method) {
                $apiTag = $method->getApiTag();
                if (!$apiTag) {
                    continue;
                }
                $permissions = $method->getOtherTags()[$apiPermission->tagName()] ?? [];
                $permission = $permissions[0] ?? $apiPermission;
                $apiPath = $permission->path ?? $apiTag->path;
                $annotationPermission[] = $apiPath;
                $permissionList[$apiPath] = [
                    'path'    => $apiPath,
                    'method'  => $permission->method ?? ($method->getMethodTag() ? $method->getMethodTag()->allow : [
                            'get',
                            'post',
                            'delete',
                            'put',
                            'patch',
                            'options',
                            'head',
                            'track',
                        ]),
                    'name'    => $permission->name ?? $apiTag->name,
                    'display' => $permission->display,
                ];
            }
            $data[$sort][$name] = $annotationPermission;
        }
        ksort($data);
        ksort($menuList);
        $this->scanPermissionList[$path] = [
            'group'      => $data,
            'permission' => $permissionList,
            'menu'       => $menuList,
        ];
        return $this->scanPermissionList[$path];
    }

    /**
     * @param      $array
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function getValue($array, $key, $default = null)
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
     * @param array $array
     * @param       $path
     * @param       $value
     */
    public function setValue(array &$array, $path, $value): void
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
     * @param string $path
     * @param string $roleId
     *
     * @return array
     * @throws CasbinException
     */
    public function getPermissionsByRoleId(string $path, string $roleId)
    {
        $scanPermission = $this->scanPermission($path);
        $userPermissions = $this->getEnforcer()->getPermissionsForUser($roleId);
        $userPermissions = array_column($userPermissions, 1);
        $data = [];
        foreach ($scanPermission['group'] as $group) {
            foreach ($group as $groupName => $apiPermissions) {
                $checkPermission = [];
                foreach ($apiPermissions as $key => $apiPath) {
                    $apiPermission = $scanPermission['permission'][$apiPath] ?? [];
                    if (empty($apiPermission)) {
                        continue;
                    }
                    if (!$apiPermission['display']) {
                        continue;
                    }
                    $checkPermission[] = [
                        'path'     => $apiPermission['path'],
                        'selected' => in_array($apiPermission['path'], $userPermissions),
                        'name'     => $apiPermission['name'],
                    ];
                }
                $hadPermission = $this->getValue($data, $groupName, []);
                $newPermission = array_merge($hadPermission, $checkPermission);
                $this->setValue($data, $groupName, $newPermission);
            }
        }
        return $data;
    }

    /**
     * @return Enforcer
     * @throws CasbinException
     */
    public function getEnforcer(): Enforcer
    {
        if (!$this->enforcer) {
            $this->enforcer = (new Casbin())->getEnforcer();
        }
        return $this->enforcer;
    }

    /**
     * @param string $path
     * @param string $roleId
     * @param array  $permissions
     * @throws CasbinException
     * @return bool
     */
    public function generatePermission(string $path, string $roleId, array $permissions): bool
    {
        $this->getEnforcer()->deleteRole($roleId);
        $scanPermission = Permission::getInstance()->scanPermission($path);
        $status = true;
        foreach ($permissions as $permission) {
            $apiPermission = $scanPermission['permission'][$permission] ?? [];
            if (empty($apiPermission)) {
                $status = false;
                break;
            }
        }
        if (!$status) {
            return $status;
        }
        foreach ($scanPermission['permission'] as $item) {
            if ($item['display']) {
                continue;
            }
            $permissions[] = $item['path'];
        }
        foreach ($permissions as $permission) {
            $apiPermission = $scanPermission['permission'][$permission] ?? [];
            foreach ($apiPermission['method'] as $method) {
                $this->getEnforcer()->addPermissionForUser($roleId, $apiPermission['path'], $method);
            }
        }
        return $status;
    }
}
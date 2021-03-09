<?php

namespace EasySwooleTool\HyperfOrm\Permission;

use Casbin\Enforcer;

class Permission
{
    /**
     * @var Enforcer
     */
    protected $enforcer;

    public function __construct()
    {
        $this->enforcer = (new Casbin())->getEnforcer();
    }

    /**
     * @param string $obj
     *
     * @return array
     */
    public function getRouterPermission(string $obj): array
    {
        return [];
    }


}
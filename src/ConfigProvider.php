<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace EasySwoole\HyperfOrm\Permission;

use EasySwoole\HyperfOrm\Permission\Adapters\DatabaseAdapter;
use EasySwoole\HyperfOrm\Permission\Models\Rule;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                DatabaseAdapter::class => DatabaseAdapter::class,
                Rule::class => Rule::class
            ],
            'publish' => [
                [
                    'id' => 'permission',
                    'description' => 'The config for permission.',
                    'source' => __DIR__ . '/Configs/permission.php',
                    'destination' => EASYSWOOLE_ROOT . '/App/Configs/permission.php',
                ],
                [
                    'id' => 'casbin',
                    'description' => 'The config for casbin config.',
                    'source' => __DIR__ . '/Configs/casbin-rbac-model.conf',
                    'destination' => EASYSWOOLE_ROOT . '/App/Storage/Casbin/casbin-rbac-model.conf',
                ],
            ],
        ];
    }
}

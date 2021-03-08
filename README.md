<p align="center">
    <a href="https://www.easyswoole.com/" target="_blank">
        <img src="https://raw.githubusercontent.com/easy-swoole/easyswoole/3.x/easyswoole.png" height="100px">
    </a>
    <h1 align="center">EasySwoole Hyperf Orm Permission </h1>
    <br>
</p>

Install
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require easyswoole-tool/hyperf-orm-permission dev-master
```

or add

```
"easyswoole-tool/hyperf-orm-permission": "dev-master"
```
to the require section of your `composer.json` file.


Dependent 
-------------------
[easyswoole/hyperf-orm](https://github.com/easy-swoole/hyperf-orm/)

Sql
--------------------
```sql
CREATE TABLE  if not exists  `casbin_rules` (
  `id` BigInt(20) unsigned NOT NULL AUTO_INCREMENT,
  `ptype` varchar(255) DEFAULT NULL,
  `v0` varchar(255) DEFAULT NULL,
  `v1` varchar(255) DEFAULT NULL,
  `v2` varchar(255) DEFAULT NULL,
  `v3` varchar(255) DEFAULT NULL,
  `v4` varchar(255) DEFAULT NULL,
  `v5` varchar(255) DEFAULT NULL,
  `create_at` int NULL DEFAULT NULL,
  `update_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;
```

Config
------------
// dev.php
```php
    <?php
    
    return [
        /*
        * Casbin model setting.
        */
        'model' => [
            // Available Settings: "file", "text"
            'config_type' => 'file',
    
            'config_file_path' => __DIR__ . '/casbin-rbac-model.conf',
    
            'config_text' => '',
        ],
    
        /*
        * Casbin adapter .
        */
        'adapter' => \EasySwooleTool\HyperfOrm\Permission\Adapters\DatabaseAdapter::class,
    
        /*
        * Database setting.
        */
        'database' => [
            // Database connection for following tables.
            'connection' => '',
    
            // Rule table name.
            'rules_table' => 'rules',
        ],
    
        'log' => [
            // changes whether Lauthz will log messages to the Logger.
            'enabled' => false,
        ],
    ];

```

DI
------------
// EasySwooleEvent.php
    <?php
        use EasySwooleTool\HyperfOrm\Permission\Adapters\DatabaseAdapter;
        use EasySwooleTool\HyperfOrm\Permission\Models\Rule;
        use EasySwoole\Component\Di;
        
        Di::getInstance()->set(DatabaseAdapter::class, DatabaseAdapter::class);
        Di::getInstance()->set(Rule::class,  Rule::class, []);
Use
------
Once installed you can do stuff like this:
```php
        
    use EasySwooleTool\HyperfOrm\Permission\Casbin;

    $casbin = (new Casbin())->getEnforcer();
    // adds permissions to a user
    $casbin->addPermissionForUser('eve', 'articles', 'read');
    // adds a role for a user.
    $casbin->addRoleForUser('eve', 'writer');
    // adds permissions to a rule
    $casbin->addPolicy('writer', 'articles', 'edit');
```
You can check if a user has a permission like this:
```php
    // to check if a user has permission
    if ($casbin->enforce('eve', 'articles', 'edit')) {
      // permit eve to edit articles
    } else {
      // deny the request, show an error
    }
```

Using Enforcer Api
-----------------

It provides a very rich api to facilitate various operations on the Policy:

Gets all roles:

```php
Enforcer::getAllRoles(); // ['writer', 'reader']
```

Gets all the authorization rules in the policy.:

```php
Enforcer::getPolicy();
```

Gets the roles that a user has.

```php
Enforcer::getRolesForUser('eve'); // ['writer']
```

Gets the users that has a role.

```php
Enforcer::getUsersForRole('writer'); // ['eve']
```

Determines whether a user has a role.

```php
Enforcer::hasRoleForUser('eve', 'writer'); // true or false
```

Adds a role for a user.

```php
Enforcer::addRoleForUser('eve', 'writer');
```

Adds a permission for a user or role.

```php
// to user
Enforcer::addPermissionForUser('eve', 'articles', 'read');
// to role
Enforcer::addPermissionForUser('writer', 'articles','edit');
```

Deletes a role for a user.

```php
Enforcer::deleteRoleForUser('eve', 'writer');
```

Deletes all roles for a user.

```php
Enforcer::deleteRolesForUser('eve');
```

Deletes a role.

```php
Enforcer::deleteRole('writer');
```

Deletes a permission.

```php
Enforcer::deletePermission('articles', 'read'); // returns false if the permission does not exist (aka not affected).
```

Deletes a permission for a user or role.

```php
Enforcer::deletePermissionForUser('eve', 'articles', 'read');
```

Deletes permissions for a user or role.

```php
// to user
Enforcer::deletePermissionsForUser('eve');
// to role
Enforcer::deletePermissionsForUser('writer');
```

Gets permissions for a user or role.

```php
Enforcer::getPermissionsForUser('eve'); // return array
```

Determines whether a user has a permission.

```php
Enforcer::hasPermissionForUser('eve', 'articles', 'read');  // true or false
```

See [Casbin API](https://casbin.org/docs/en/management-api) for more APIs.
<?php

declare(strict_types=1);

namespace EasySwoole\HyperfOrm\Permission\Models;

use EasySwoole\EasySwoole\Config;
use EasySwoole\HyperfOrm\Model;

class Rule extends Model
{

    const CREATED_AT = 'create_at';

    const UPDATED_AT = 'update_at';

    protected $dateFormat = 'U';

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = ['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ptype'     => 'string',
        'create_at' => 'datetime',
        'update_at' => 'datetime',
        'v0'        => 'string',
        'v1'        => 'string',
        'v2'        => 'string',
        'v3'        => 'string',
        'v4'        => 'string',
        'v5'        => 'string',
    ];

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        $config = Config::getInstance()->getConf('permission.database.rules_table');
        $this->setTable($config ?? 'rules');

        parent::__construct($attributes);
    }
}

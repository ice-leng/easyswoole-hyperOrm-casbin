<?php

declare(strict_types=1);

namespace EasySwooleTool\HyperfOrm\Permission\Models;

use EasySwoole\EasySwoole\Config;
use EasySwoole\HyperfOrm\Model;

class Rule extends Model
{
    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = ['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $config = Config::getInstance()->getConf('permission.database.rules_table');
        $this->setTable($config ?? 'rules');

        parent::__construct($attributes);
    }
}

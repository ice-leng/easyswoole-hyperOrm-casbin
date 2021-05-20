<?php

use EasySwoole\HyperfOrm\Permission\Adapters\DatabaseAdapter;
use EasySwoole\HyperfOrm\Permission\Models\Rule;

return [
    DatabaseAdapter::class => DatabaseAdapter::class,
    Rule::class => Rule::class
];

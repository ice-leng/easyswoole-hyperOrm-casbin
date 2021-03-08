<?php

use EasySwooleTool\HyperfOrm\Permission\Adapters\DatabaseAdapter;
use EasySwooleTool\HyperfOrm\Permission\Models\Rule;

return [
    DatabaseAdapter::class => DatabaseAdapter::class,
    Rule::class => Rule::class
];

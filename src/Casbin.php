<?php

namespace EasySwooleTool\HyperfOrm\Permission;

use Casbin\Enforcer;
use Casbin\Exceptions\CasbinException;
use Casbin\Model\Model;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Config;
use EasySwooleTool\HyperfOrm\Permission\Adapters\DatabaseAdapter;

class Casbin
{
    public $enforcer;

    /**
     * @var DatabaseAdapter
     */
    public $adapter;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var bool
     */
    public $log;

    /**
     * @var array
     */
    public $config = [];

    public function __construct($config = [])
    {
        $configs = Config::getInstance()->getConf('permission');
        $this->config = $this->mergeConfig($configs ?? [], $config);

        $this->adapter = $this->config['adapter'];
        if (!is_null($this->adapter)) {
            $this->adapter = Di::getInstance()->get($this->adapter);
        }

        $this->model = new Model();
        if ('file' == $this->config['model']['config_type']) {
            $this->model->loadModel($this->config['model']['config_file_path']);
        } elseif ('test' == $this->config['model']['config_type']) {
            $this->model->loadModel($this->config['model']['config_text']);
        }

        $this->log = $this->config['log']['enabled'] ?: false;
    }

    /**
     * @param bool $newInstance
     *
     * @return Enforcer
     * @throws CasbinException
     */
    public function getEnforcer($newInstance = false): Enforcer
    {
        if ($newInstance || is_null($this->enforcer)) {
            $this->enforcer = new Enforcer($this->model, $this->adapter, $this->log);
        }

        return $this->enforcer;
    }

    private function mergeConfig(array $a, array $b)
    {
        foreach ($a as $key => $val) {
            if (isset($b[$key])) {
                if (gettype($a[$key]) != gettype($b[$key])) {
                    continue;
                }
                if (is_array($a[$key])) {
                    $a[$key] = $this->mergeConfig($a[$key], $b[$key]);
                } else {
                    $a[$key] = $b[$key];
                }
            }
        }
        return $a;
    }
}

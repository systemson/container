<?php

namespace Amber\Container\Config;

trait ConfigAware
{
    /**
     * @var array The config values.
     */
    protected $config = [];

    public function setConfig(array $config)
    {
        foreach($config as $key => $value)
        {
            $this->config[$key] = $value;
        }
    }

    public function getConfig(string $key, $default = null)
    {
        $config = $this->config;

        foreach (explode('.', $key) as $search) {

            if (isset($config[$search])) {
                $config = $config[$search];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
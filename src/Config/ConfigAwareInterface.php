<?php

namespace Amber\Container\Config;

interface ConfigAwareInterface
{
    const ENVIRONMENT = 0;

    const CACHE_DRIVER = 'file';

    public function setConfig(array $config);

    public function getConfig(string $key, $default = null);
}

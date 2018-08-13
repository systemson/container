<?php

namespace Amber\Container\Config;

use Amber\Config\ConfigAwareTrait as BaseConfig;

trait ConfigAwareTrait
{
    use BaseConfig;

    /**
     * Returns the Container's cache driver.
     *
     * @return string The cache driver's name.
     */
    protected function getCacheDriverNameConfig()
    {
        return $this->getConfig('cache_driver', static::CACHE_DRIVER);
    }

    /**
     * Returns the cache name for the services map.
     *
     * @return string The cache name.
     */
    protected function getCacheServicesNameConfig()
    {
        return $this->getConfig('cache_services_name', static::CACHE_SERVICES_NAME);
    }
}

<?php

namespace Amber\Container\Container;

use Amber\Cache\Cache;
use Amber\Container\Config\ConfigAwareInterface;
use Psr\SimpleCache\CacheInterface;

trait CacheHandler
{
    /**
     * @var Cache driver.
     */
    protected $cacher;

    /**
     * @var Cache driver.
     */
    protected $servicesName = 'injector_services';

    /**
     * Returns the cache driver.
     *
     * @return object Psr\SimpleCache\CacheInterface instance.
     */
    public function cache()
    {
        /* Checks if the CacheInterface is already instantiated. */
        if (!$this->cacher instanceof CacheInterface) {
            $this->cacher = Cache::driver($this->getConfig('cache_driver', ConfigAwareInterface::CACHE_DRIVER));
        }

        return $this->cacher;
    }

    /**
     * Retrieves the Container's map from the cache.
     *
     * @return void
     */
    public function pick()
    {
        $this->services = $this->cache()->get($this->servicesName, []);

        return true;
    }

    /**
     * Stores the Container's map into the cache.
     *
     * @return void
     */
    public function drop()
    {
        if (!$this->cache()->has($this->servicesName)) {
            $this->cache()->set($this->servicesName, $this->services);
        }

        return true;
    }
}

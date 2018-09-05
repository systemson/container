<?php

namespace Amber\Container\Container;

use Amber\Cache\Cache;
use Amber\Cache\CacheAware\CacheAwareTrait;
use Amber\Cache\Driver\CacheDriver;
use Amber\Collection\Collection;

trait CacheHandler
{
    use CacheAwareTrait;

    /**
     * Retrieves the Container's map from the cache.
     *
     * @return boolen true
     */
    public function pick()
    {
        $this->setCollection(
            new Collection(
                $this->getCache()->get(
                    $this->getConfig('cache_services_name'),
                    []
                )
            )
        );

        return true;
    }

    /**
     * Stores the Container's map into the cache.
     *
     * @return void
     */
    public function drop()
    {
        if (!$this->getCache()->has($this->getConfig('cache_services_name'))) {
            $this->getCache()->set(
                $this->getConfig('cache_services_name'),
                $this->getCollection()->toArray()
            );
        }

        return true;
    }
}

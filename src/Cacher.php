<?php

namespace Amber\Container;

use Amber\Cache\Cache;

trait Cacher
{
    protected $cache;

    public function setCache($driver = null)
    {
        $this->cache = $this->getInstanceOf(Cache::class, [$driver]);
    }

    protected function cache() {
        return $this->cache;
    }
}

<?php

namespace Amber\Container\Traits;

use Psr\SimpleCache\CacheInterface;
use Amber\Collection\Collection;

trait CacheHandlerTrait
{
    public function drop(): void
    {
        $cache = $this->getCache();
        $name = $this->cacheName();
        $content = $this->getCollection();

        $array = $cache->get($name, []);

        if (array_diff($content->keys(), $array) == []) {
            return;
        }

        foreach ($content as $key => $value) {
            if (!$cache->has($key)) {
                $cache->set($key, $value);
                $array[] = $key;
            }
        }

        $cache->set($name, $array);
    }

    public function pick(): void
    {
        $cache = $this->getCache();
        $name = $this->cacheName();

        $list = $cache->get($name, []);

        $content = [];
        foreach ($list as $key) {
            if ($cache->has($key)) {
                $value = $cache->get($key);
                $content[$key] = $value;
            }
        }

        $this->getCollection()->exchangeArray($content);
    }

    private function cacheName(): string
    {
        return '_container';
    }
}

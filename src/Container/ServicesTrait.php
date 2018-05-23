<?php

namespace Amber\Container\Container;

use Amber\Cache\Cache;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Service\Service;

trait ServicesTrait
{
    /**
     * Binds or Updates an item to the Container's map by a unique key.
     *
     * @param string $key The unique item's key.
     * @param mixed  $key The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool True on success. False if key already exists.
     */
    public function put($key, $value = null)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        $this->services[$key] = new Service($key, $value ?? $key);

        return true;
    }

    /**
     * Updates an item on the Container's map by a unique key.
     *
     * @param string $key The unique item's key.
     * @param mixed  $key The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     * @throws Amber\Container\Exception\NotFoundException
     *
     * @return bool true
     */
    public function update($key, $value = null)
    {
        if (!$this->has($key)) {
            throw new NotFoundException("No entry was found for {$key}");
        }

        return $this->put($key, $value);
    }

    /**
     * Clears the Container's map.
     *
     * @return void
     */
    public function clear()
    {
        $this->services = [];
    }

    /**
     * Counts the items in the Container's map.
     *
     * @return int
     */
    public function count()
    {
        return count($this->services);
    }

    /**
     * Counts the items in the Container's map.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count() == 0;
    }

    /**
     * Returns the items in the Container's map.
     *
     * @return array The Container's map.
     */
    public function services()
    {
        return $this->services;
    }

    /**
     * Retrieves the Container's map from the cache.
     *
     * @return void
     */
    public function init()
    {
        $this->services = Cache::get('services', []);
    }

    /**
     * Stores the Container's map into the cache.
     *
     * @return void
     */
    public function build()
    {
        Cache::set('services', $this->services);
    }
}

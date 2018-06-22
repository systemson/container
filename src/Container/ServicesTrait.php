<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\ContainerException;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Service\Service;

trait ServicesTrait
{
    /**
     * Binds or Updates an item to the Container's map by a unique key.
     *
     * @param string $key   The unique item's key.
     * @param mixed  $value The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool True on success.
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
     * Sets an instance into a Service in the Container's map by it's unique key.
     *
     * @param string $key      The unique item's key.
     * @param mixed  $instance The value of the item.
     *
     * @throws Amber\Container\Exception\ContainerException
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool True on success.
     */
    public function set($key, $instance)
    {
        if (!$this->has($key)) {
            throw new NotFoundException("No entry was found for {$key}.");
        }

        $service = $this->locate($key);

        if ($service->type != 'class') {
            throw new ContainerException("Service {$key} is not a class.");
        }

        if (is_object($instance) && $instance instanceof $service->value) {
            $service->setInstance($instance);
            $service->singleton();

            return true;
        }

        throw new InvalidArgumentException("Argument provided for {$key} is not an instance of {$service->value} class.");
    }

    /**
     * Updates an item in the Container's map by it's unique key.
     *
     * @param string $key   The unique item's key.
     * @param mixed  $value The value of the item.
     *
     * @throws Amber\Container\Exception\NotFoundException
     *
     * @return bool true
     */
    public function update($key, $value)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        if (!$this->has($key)) {
            throw new NotFoundException("No entry was found for {$key}.");
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

        return true;
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
}

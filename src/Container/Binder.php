<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Common\Validator;
use Amber\Container\Service;
use Psr\Container\ContainerInterface;

/**
 * Class to handle the Container's binder.
 */
abstract class Binder implements ContainerInterface
{
    use Finder, Injector, Validator;

    protected $services = [];

    /**
     * Bind an item to the Container's map by a unique key.
     *
     * @param string $key The unique item's key.
     * @param mixed  $key The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bolean true
     */
    public function bind($key, $value = null)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        $this->services[$key] = new Service($value ?? $key);

        return true;
    }

    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     * @throws Amber\Container\Exception\NotFoundException
     *
     * @return mixed The value of the item.
     */
    public function get($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        if ($this->has($key)) {

            $service = $this->locate($key);

            if (!$this->isClass($service->value)) {

                return $service->value;

            } else {

                $arguments = $this->getArguments($service->parameters());

                return $service->instance($arguments);
            }
        }

        throw new NotFoundException("No entry was found in for key {$key}");
    }

    /**
     * Checks for an item on the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool
     */
    public function has($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        return isset($this->services[$key]) ?? false;
    }

    /**
     * Removes an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bolean true on succes, false on failure.
     */
    public function unbind($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        if (isset($this->services[$key])) {
            unset($this->services[$key]);

            return true;
        }

        return false;
    }

    /**
     * Binds multiple items to the Container's map by their unique keys.
     *
     * @param array $items The array of items to add.
     *
     * @return bolean true
     */
    public function bindMultiple(array $items)
    {
        foreach ($items as $key => $value) {
            $this->bind($key, $value);
        }

        return true;
    }

    /**
     * Removes multiple items from the Container's map by their unique keys.
     *
     * @param array $items The array of items to remove.
     *
     * @return bolean true
     */
    public function unbindMultiple(array $array)
    {
        foreach ($array as $key) {
            $this->unbind($key);
        }

        return true;
    }
}

<?php

namespace Amber\Container\Container;

use Amber\Common\Validator;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Service\Service;
use Psr\Container\ContainerInterface;

/**
 * Class to handle the Container's binder.
 */
abstract class Binder implements ContainerInterface
{
    use Finder, Validator;

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
    final public function bind($key, $value = null)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        $this->services[$key] = new Service($key, $value ?? $key);

        return true;
    }

    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return mixed The value of the item.
     */
    final public function get($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        /* Retrieves the service from the map. */
        $service = $this->locate($key);

        if (!$this->isClass($service->value)) {
            return $service->value;
        }

        return $this->instanciate($service);
    }

    /**
     * Checks for the existance of an item on the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool
     */
    final public function has($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        return isset($this->services[$key]);
    }

    /**
     * Unbind an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool true on succes, false on failure.
     */
    final public function unbind($key)
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
     * @param array $items An array of key => value items to bind.
     *
     * @return bool true
     */
    final public function bindMultiple(array $items)
    {
        foreach ($items as $key => $value) {
            $this->bind($key, $value);
        }

        return true;
    }

    /**
     * Gets multiple items from the Container's map by their unique keys.
     *
     * @param array $items An array of items to get.
     *
     * @return bool true
     */
    final public function getMultiple(array $items)
    {
        foreach ($items as $key) {
            $services[] = $this->get($key);
        }

        return $services;
    }

    /**
     * Removes multiple items from the Container's map by their unique keys.
     *
     * @param array $items An array of items to remove.
     *
     * @return bool true
     */
    final public function unbindMultiple(array $array)
    {
        foreach ($array as $key) {
            $this->unbind($key);
        }

        return true;
    }

    /**
     * Get an instance of the specified Service.
     *
     * @todo Should be moved to a independent trait.
     *
     * @param string $class     The service to be instantiated.
     * @param array  $arguments Optional. The arguments for the constructor.
     *
     * @return object The instance of the class
     */
    protected function instanciate(Service $service, $arguments = [])
    {
        if (empty($service->arguments)) {
            $service->setArguments($this->getArguments($service, $arguments));
        }

        return $service->getInstance($service->arguments);
    }
}

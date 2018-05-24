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
    use Finder, ServicesTrait, MultipleBinder, Validator;

    protected $services = [];

    /**
     * Binds an item to the Container's map by a unique key.
     *
     * @param string $key   The unique item's key.
     * @param mixed  $value The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool True on success. False if key already exists.
     */
    final public function bind($key, $value = null)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        if (!$this->has($key)) {
            return $this->put($key, $value);
        }

        return false;
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
     * Unbinds an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bool true on success, false on failure.
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
     * Gets an instance of the specified Service.
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

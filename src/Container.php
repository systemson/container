<?php

namespace Amber\Container;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Service\ServiceClass;
use Amber\Collection\CollectionAware\CollectionAwareInterface;
use Amber\Collection\CollectionAware\CollectionAwareTrait;
use Amber\Container\Config\ConfigAwareTrait;
use Amber\Container\Config\ConfigAwareInterface;
use Psr\Container\ContainerInterface;
use Amber\Validator\Validator;
use Amber\Collection\Collection;
use Amber\Container\Base\MultipleBinder;

/**
 * Class for PSR-11 Container compliance.
 */
class Container implements ContainerInterface, ConfigAwareInterface, CollectionAwareInterface
{
    use ConfigAwareTrait, CollectionAwareTrait, MultipleBinder, Validator;

    /**
     * The Container constructor.
     *
     * @param array $config The configurations for the Container.
     */
    public function __construct()
    {
        $this->initCollection();
    }

    public function initCollection()
    {
        $this->setCollection(new Collection());
    }

    /**
     * Returns a Service from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\NotFoundException
     *
     * @return mixed The value of the item.
     */
    public function locate($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string.');
        }

        if (!$this->has($key)) {
            throw new NotFoundException("No entry was found for \"{$key}\".");
        }

        return $this->getCollection()->get($key);
    }

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
            throw new InvalidArgumentException('Key argument must be a non empty string.');
        }

        if (!$this->has($key)) {
            return $this->put($key, $value);
        }

        return false;
    }

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
            throw new InvalidArgumentException('Key argument must be a non empty string.');
        }

        if (!$this->isClass($value ?? $key)) {
            $this->getCollection()->put($key, $value);
            return true;
        }

        $this->getCollection()->put($key, new ServiceClass($value ?? $key, $this));
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
            throw new InvalidArgumentException('Key argument must be a non empty string.');
        }

        /* Retrieves the service from the map. */
        $service = $this->locate($key);

        if (!$service instanceof ServiceClass) {
            return $service;
        }

        return $this->instantiate($service);
    }

    /**
     * Gets an instance of the specified Service.
     *
     * @param string $service   The service to be instantiated.
     * @param array  $arguments Optional. The arguments for the constructor.
     *
     * @return object The instance of the class
     */
    protected function instantiate(ServiceClass $service)
    {
        return $service->getInstance($this->getArguments($service));
    }

    /**
     * Gets the arguments for a Service's constructor.
     *
     * @param array $service   The params needed by the constructor.
     * @param array $arguments Optional. The arguments previously passed to the container.
     *
     * @return array The arguments for the class constructor.
     */
    protected function getArguments(ServiceClass $service)
    {
        $params = $service->getParameters();

        if (empty($params)) {
            return [];
        }

        $arguments = [];

        foreach ($params as $param) {
            $key = !is_null($param->getClass()) ? $param->getClass()->getName() : $param->name;

            if ($service->hasArgument($key)) {
                $subService = $service->getArgument($key);

                if (!$subService instanceof ServiceClass) {
                    $arguments[] = $subService;
                } else {
                    $arguments[] = $this->instantiate($subService);
                }

            } else {
                try {
                    $arguments[] = $this->get($key);
                } catch (NotFoundException $e) {
                    if (!$param->isOptional()) {
                        throw new NotFoundException("No entry was found for \"{$key}\".");
                    }
                }
            }
        }

        return $arguments;
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
            throw new InvalidArgumentException('Key argument must be a non empty string.');
        }

        return $this->getCollection()->has($key);
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
            throw new InvalidArgumentException('Key argument must be a non empty string.');
        }

        return $this->getCollection()->delete($key);
    }

    /**
     * Clears the Container's map.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->getCollection()->clear();
    }

    /**
     * Binds and Gets an item from the Container's map by its unique key.
     *
     * @param string $class The item's class.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return mixed The value of the item.
     */
    public function make(string $class)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isClass($class)) {
            throw new InvalidArgumentException("Argument \"$class\" must be a valid class.");
        }

        $this->bind($class);

        return $this->get($class);
    }

    /**
     * Binds an item to the Container and return the service.
     *
     * @param string $class The item's class.
     * @param string $class The item's alias.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return ServiceClass
     */
    public function register(string $class, string $alias = null): ServiceClass
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isClass($class)) {
            throw new InvalidArgumentException("Argument \"$class\" must be a valid class.");
        }

        $alias = $alias ?? $class;

        $this->bind($alias, $class);

        return $this->locate($alias);
    }

    public function getClosureFor(string $class, string $method, array $args = [])
    {
        $instance = $this->make($class);

        return \Closure::fromCallable(function () use ($instance, $method, $args) {
            return call_user_func_array([$instance, $method], $args);
        });
    }
}

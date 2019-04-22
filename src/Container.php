<?php

namespace Amber\Container;

use Amber\Cache\CacheAware\CacheAwareInterface;
use Amber\Cache\CacheAware\CacheAwareTrait;
use Amber\Collection\Collection;
use Amber\Collection\CollectionAware\CollectionAwareInterface;
use Amber\Collection\CollectionAware\CollectionAwareTrait;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Service\ServiceClass;
use Amber\Validator\Validator;
use Psr\Container\ContainerInterface;
use Amber\Container\Traits\MultipleBinderTrait;
use Amber\Container\Traits\CacheHandlerTrait;
use Closure;

/**
 * Class for PSR-11 Container compliance.
 */
class Container implements ContainerInterface, CollectionAwareInterface, CacheAwareInterface
{
    use CollectionAwareTrait, CacheAwareTrait, MultipleBinderTrait, CacheHandlerTrait, Validator;

    /**
     * The Container constructor.
     */
    public function __construct()
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
            InvalidArgumentException::mustBeString();
        }

        if (!$this->has($key)) {
            NotFoundException::throw($key);
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
            InvalidArgumentException::mustBeString();
        }

        return $this->put($key, $value);
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
    public function put($key, $value = null): bool
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            InvalidArgumentException::mustBeString();
        }

        /* Throws an InvalidArgumentException on invalid type. */
        if (is_null($value) && !$this->isClass($key)) {
            InvalidArgumentException::mustBeClass($key);
        }

        if (!$this->isClass($value ?? $key)) {
            return $this->getCollection()->add($key, $value);
        }

        return $this->getCollection()->add($key, new ServiceClass($value ?? $key));
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
            InvalidArgumentException::mustBeString();
        }

        /* Retrieves the service from the map. */
        $service = $this->locate($key);

        if ($service instanceof ServiceClass) {
            return $this->instantiate($service);
        } elseif ($service instanceof Closure) {
            return $service();
        }

        return $service;
    }

    /**
     * Gets an instance of the specified Service.
     *
     * @param string $service   The service to be instantiated.
     *
     * @return object The instance of the class
     */
    protected function instantiate(ServiceClass $service)
    {
        return $service->getInstance($this->getArguments($service));
    }

    /**
     * Gets the arguments for a Service's method.
     *
     * @param array $service The params needed by the constructor.
     * @param array $method  Optional. The method to get the arguments from.
     *
     * @return array The arguments for the class method.
     */
    protected function getArguments(ServiceClass $service, string $method = '__construct'): array
    {
        $params = $service->getParameters($method);

        if (empty($params)) {
            return [];
        }

        $arguments = [];

        foreach ($params as $param) {
            if (!is_null($param->getClass())) {
                $key = $param->getClass()->getName();
            } else {
                $key = $param->name;
            }

            try {
                $arguments[] = $this->getArgumentFromService($service, $key) ?? $this->get($key);
            } catch (NotFoundException $e) {
                if (!$param->isOptional()) {
                    $msg = $e->getMessage() . " Requested on [{$service->class}::{$method}()].";
                    throw new NotFoundException($msg);
                }
            }
        }

        return $arguments;
    }

    /**
     * Gets the arguments for a Service's method from the it's arguments bag.
     *
     * @param array $service The params needed by the constructor.
     * @param array $key     The argument's key.
     *
     * @return mixed The argument's value.
     */
    protected function getArgumentFromService(ServiceClass $service, string $key)
    {
        if (!$service->hasArgument($key)) {
            return;
        }

        $subService = $service->getArgument($key);

        if (!$subService instanceof ServiceClass) {
            return $subService;
        }

        return $this->instantiate($subService);
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
            InvalidArgumentException::mustBeString();
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
            InvalidArgumentException::mustBeString();
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
            InvalidArgumentException::mustBeClass($class);
        }

        $this->bind($class);

        return $this->get($class);
    }

    /**
     * Binds an item to the Container and return the service.
     *
     * @param string $class The item's class.
     * @param string $alias The item's alias.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return ServiceClass
     */
    public function register(string $class, string $alias = null): ServiceClass
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isClass($class)) {
            InvalidArgumentException::mustBeClass($class);
        }

        $alias = $alias ?? $class;

        $this->bind($alias, $class);

        return $this->locate($alias);
    }

    /**
     * Binds an item to the Container and return the service.
     *
     * @param string $class The item's class.
     * @param string $alias The item's alias.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return ServiceClass
     */
    public function singleton(string $class, string $alias = null): ServiceClass
    {
        $service = $this->register($class, $alias);
        $service->singleton();

        return $service;
    }

    /**
     * Gets a closure for calling a method of the provided class.
     *
     * @param string $class  The class to instantiate.
     * @param string $method The class method to call.
     * @param array  $binds  The arguments for the method.
     *
     * @return Closure
     */
    public function getClosureFor(string $class, string $method, array $binds = []): Closure
    {
        $instance = $this->make($class);
        $service = $this->locate($class)->setArguments($binds);

        $args = $this->getArguments($service, $method);

        return Closure::fromCallable(function () use ($instance, $method, $args) {
            return call_user_func_array([$instance, $method], $args);
        });
    }
}

<?php

namespace Amber\Container;

use Amber\Cache\CacheAware\CacheAwareInterface;
use Amber\Collection\{
    Collection,
    CollectionAware\CollectionAwareInterface,
    CollectionAware\CollectionAwareTrait,
};
use Amber\Container\{
    Exception\InvalidArgumentException,
    Exception\NotFoundException,
    Service\ServiceClass,
    Traits\MultipleBinderTrait,
    Traits\CacheHandlerTrait,
};
use Amber\Validator\Validator;
use Psr\Container\ContainerInterface;
use Closure;

/**
 * Class for PSR-11 Container compliance.
 */
class Container implements ContainerInterface, CollectionAwareInterface, CacheAwareInterface
{
    use CollectionAwareTrait, MultipleBinderTrait, CacheHandlerTrait, Validator;

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
     * @return bool True on success. False if key already exists.
     */
    public function put($key, $value = null): bool
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            InvalidArgumentException::mustBeString();
        }

        /* Throws an InvalidArgumentException on invalid type. */
        if (is_null($value) && !$this->isClass($key)) {
            InvalidArgumentException::identifierMustBeClass($key);
        }

        $value = $value ?? $key;

        if ($this->isClass($key, $value)) {
            if (is_a($value, $key, true)) {
                return $this->getCollection()->add($key, new ServiceClass($value));
            } else {
                throw new InvalidArgumentException("Class [$value] must be a subclass of [$key], or the same class.");
            }
        }

        return $this->getCollection()->add($key, $value);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws InvalidArgumentException    Identifier argument must be a non empty string.
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
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
            InvalidArgumentException::identifierMustBeClass($class);
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
            InvalidArgumentException::identifierMustBeClass($class);
        }

        $alias = $alias ?? $class;

        $this->bind($alias, $class);

        return $this->locate($alias);
    }

    /**
     * Binds an item to the Container as singleton and return the service.
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
        return $this->register($class, $alias)
        ->singleton();
    }

    /**
     * Gets a closure for calling a method of the provided class.
     *
     * @param string $class  The class to instantiate.
     * @param string $method The class method to call.
     * @param array  $binds  The arguments for the service.
     *
     * @return Closure
     */
    public function getClosureFor(string $class, string $method, array $binds = []): Closure
    {
        $instance = $this->make($class);

        $service = $this->locate($class)
        ->setArguments($binds);

        $args = $this->getArguments($service, $method);

        return Closure::fromCallable(function () use ($instance, $method, $args) {
            return call_user_func_array([$instance, $method], $args);
        });
    }
}

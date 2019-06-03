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
     * Returns a Service from the Container's map by its identifier.
     *
     * @param string $id The entry's identifier.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException Identifier must be a non empty string.
     * @throws Amber\Container\Exception\NotFoundException        No entry was found for [$id] identifier.
     *
     * @return mixed
     */
    public function locate($id)
    {
        if (!$this->isString($id)) {
            InvalidArgumentException::mustBeString();
        }

        if (!$this->has($id)) {
            NotFoundException::throw($id);
        }

        return $this->getCollection()->get($id);
    }

    /**
     * Binds an entry to the container by its identifier.
     *
     * When no $value is provided $id must be a valid class.
     * When $id and $value are classes, $value must be a subclass of $id, or the same class.
     *
     * @param string $id    The entry's identifier.
     * @param mixed  $value Optional. The entry's value.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *         Identifier must be a non empty string.
     *         Identifier [$id] must be a valid class.
     *         Class [$value] must be a subclass of [$id], or the same.
     *
     * @return bool True on success. False if identifier already exists.
     */
    final public function bind($id, $value = null): bool
    {
        if (!$this->isString($id)) {
            InvalidArgumentException::mustBeString();
        }

        return $this->put($id, $value);
    }

    /**
     * Binds or Updates an item to the Container's map by a unique key.
     *
     * @param string $id    The entry's identifier.
     * @param mixed  $value Optional. The entry's value.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *         Identifier must be a non empty string.
     *         Identifier [$id] must be a valid class.
     *         Class [$value] must be a subclass of [$id], or the same.
     *
     * @return bool True on success. False if identifier already exists.
     */
    public function put($id, $value = null): bool
    {
        if (!$this->isString($id)) {
            InvalidArgumentException::mustBeString();
        }

        if (is_null($value) && !$this->isClass($id)) {
            InvalidArgumentException::identifierMustBeClass($id);
        }

        $value = $value ?? $id;

        if ($this->isClass($id, $value)) {
            if (is_a($value, $id, true)) {
                return $this->getCollection()->add($id, new ServiceClass($value));
            } else {
                throw new InvalidArgumentException("Class [$value] must be a subclass of [$id], or the same class.");
            }
        }

        return $this->getCollection()->add($id, $value);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id The entry's identifier.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *         Identifier must be a non empty string.
     * @throws Amber\Container\Exception\NotFoundExceptionInterface
     *         No entry was found for [$id] identifier.
     * @throws Amber\Container\Exception\ContainerExceptionInterface
     *         Error while retrieving the entry.
     *
     * @return mixed The entry.
     */
    final public function get($id)
    {
        if (!$this->isString($id)) {
            InvalidArgumentException::mustBeString();
        }

        $service = $this->locate($id);

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

        /*
         * First we find the name of the parameter or the class name.
         * Then we retrieve the parameter from the container.
         */
        foreach ($params as $param) {
            /* Check if the parameter MUST be an instance of a class */
            if (!is_null($param->getClass())) {
                // If i'ts an instance, gets the name of the clas.
                $key = $param->getClass()->getName();
            } else {
                // Else gets the parameter name.
                $key = $param->name;
            }

            // Then tries to get the argument from the service itself or from the container
            try {
                $arguments[] = $this->getArgumentFromService($service, $key) ?? $this->get($key);
            } catch (NotFoundException $e) {
                // If the parameter is not optional thows an exception.
                if (!$param->isOptional()) {
                    $msg = $e->getMessage() . " Requested on [{$service->class}::{$method}()].";
                    throw new NotFoundException($msg);
                }
                // Else returns the parameter's default value.
                $arguments[] = $param->getDefaultValue();
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
     * Wether an entry is present in the container.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id The entry's identifier.
     *
     * @return bool
     */
    final public function has($id): bool
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($id)) {
            InvalidArgumentException::mustBeString();
        }

        return $this->getCollection()->has($id);
    }

    /**
     * Unbinds an entry from the container by its identifier.
     *
     * @param string $id The entry's identifier.
     *
     * @return bool True on success. False if identifier doesn't exists.
     */
    final public function unbind($id): bool
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($id)) {
            InvalidArgumentException::mustBeString();
        }

        return $this->getCollection()->delete($id);
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

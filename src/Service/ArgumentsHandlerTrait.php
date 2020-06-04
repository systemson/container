<?php

namespace Amber\Container\Service;

use Amber\Validator\Validator;
use Amber\Container\Container;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\ContainerException;

/**
 * @todo Should replace ContainerException for a more specific exception.
 */
trait ArgumentsHandlerTrait
{
    /**
     * @var \ReflectionClass.
     */
    protected $reflection;

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * Gets an instance of the ReflectionClass for the current class.
     *
     * @return \ReflectionClass
     */
    public function getReflection(): \Reflector
    {
        if ($this->reflection instanceof \ReflectionClass) {
            return $this->reflection;
        }

        return $this->reflection = new \ReflectionClass($this->class);
    }

    public function hasMethod(string $method)
    {
        return isset($this->methods[$method]);
    }

    public function getMethod(string $method): ?ServiceMethod
    {
        if ($this->hasMethod($method)) {
            return $this->methods[$method];
        }

        if ($this->getReflection()->hasMethod($method)) {
            return $this->methods[$method] = new ServiceMethod($method, $this->getReflection()->getMethod($method));
        }

        return null;
    }

    /**
     * Gets the method paramaters.
     *
     * @return array
     */
    public function getParameters(string $method = '__construct'): array
    {
        if (($method = $this->getMethod($method)) != null) {
            return $method->getParameters();
        }

        return [];
    }

    /**
     * Stores a Service argument by its key.
     *
     * @param string $method The service method.
     * @param string $key    The argument key.
     * @param mixed  $value  The argument value.
     *
     * @return self The current service.
     */
    public function setArgument(string $method, string $key, $value = null): self
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (is_null($value) && !$this->isClass($key)) {
            InvalidArgumentException::identifierMustBeClass($key);
        }

        if (is_null($serviceMethod = $this->getMethod($method))) {
            throw new ContainerException("Method {$this->getName()}::{$method}() does not exists.");
        }

        if ($this->isClass($value ?? $key)) {
            $serviceMethod->setArgument($key, new ServiceClass($value ?? $key));
        } elseif ($value instanceof \Closure) {
            $serviceMethod->setArgument($key, new ServiceClosure($value));
        } else {
            $serviceMethod->setArgument($key, $value);
        }

        return $this;
    }

    /**
     * Whether an argument is binded to the Service.
     *
     * @param string $key The argument key.
     *
     * @return bool
     */
    public function hasArgument(string $method, string $key): bool
    {
        if (is_null($serviceMethod = $this->getMethod($method))) {
             throw new ContainerException("Method {$this->getName()}::{$method}() does not exists.");
        }

        return $serviceMethod->hasArgument($key);
    }

    /**
     * Gets a Service argument by its key.
     *
     * @param string $key The argument key.
     *
     * @return array
     */
    public function getArgument(string $method, string $key)
    {
        if (is_null($serviceMethod = $this->getMethod($method))) {
             throw new ContainerException("Method {$this->getName()}::{$method}() does not exists.");
        }

        return $serviceMethod->getArgument($key);
    }

    /**
     * Stores the Service arguments.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return self The current service.
     */
    public function setArguments(string $method, array $arguments = []): self
    {
        foreach ($arguments as $key => $value) {
            $this->setArgument($method, $key, $value);
        }

        return $this;
    }

    /**
     * Gets the Service arguments.
     *
     * @return array
     */
    public function getArguments(string $method): array
    {
        if (($methodRelection = $this->getMethod($method)) != null) {
            return $methodRelection->getArguments();
        }

        return [];
    }
}

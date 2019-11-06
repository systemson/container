<?php

namespace Amber\Container\Service;

use Amber\Validator\Validator;
use Amber\Container\Container;
use Amber\Container\Exception\InvalidArgumentException;

trait ArgumentsHandlerTrait
{
    /**
     * @var \ReflectionClass.
     */
    protected \ReflectionClass $reflection;

    /**
     * @var array
     */
    protected array $arguments = [];

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * @var array
     */
    protected array $methods = [];

    /**
     * @var array
     */
    protected array $properties = [];

    /**
     * Gets an instance of the ReflectionClass for the current class.
     *
     * @return \ReflectionClass
     */
    public function getReflection(): \ReflectionClass
    {
        if (isset($this->reflection)) {
            return $this->reflection;
        }

        return $this->reflection = new \ReflectionClass($this->class);
    }

    public function getMethod(string $name): ?ServiceMethod
    {
        if (isset($this->methods[$name])) {
            return $this->methods[$name];
        }

        if ($this->getReflection()->hasMethod($name)) {
            return $this->methods[$name] = new ServiceMethod($name, $this->getReflection()->getMethod($name));
        }

        return null;
    }

    public function getProperty(string $name): ?ServiceProperty
    {
        if (!isset($this->properties[$name])) {
            if ($this->getReflection()->hasProperty($name)) {
                return $this->properties[$name] = new ServiceProperty(
                    $name,
                    $this->getReflection()->getProperty($name)
                );
            }
        }

        return $this->properties[$name];
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
     * @todo COULD set the argument for a specified method.
     *
     * @param string $key   The argument key.
     * @param mixed  $value The argument value.
     *
     * @return self The current service.
     */
    public function setArgument(string $method, string $key, $value = null): self
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (is_null($value) && !$this->isClass($key)) {
            InvalidArgumentException::identifierMustBeClass($key);
        }

        if ($this->isClass($value ?? $key)) {
            $this->getMethod($method)->setArgument($key, new ServiceClass($value ?? $key));
        } else {
            $this->getMethod($method)->setArgument($key, $value);
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
        return $this->getMethod($method)->hasArgument($key);
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
        $value =  $this->getMethod($method)->getArgument($key);

        if ($value instanceof \Closure) {
            return $value();
        }

        return $value;
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

    public function injectProperty(string $name, $value = null): self
    {
        if (($property = $this->getProperty($name)) == null) {
            throw new \Exception("Error Processing Request", 1);
        }
        
        $property->setValue($value);

        return $this;
    }

    public function getInjectables()
    {
        return $this->properties;
    }
}

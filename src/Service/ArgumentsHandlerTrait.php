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

    public function hasMethod(string $name): bool
    {
        return $this->getReflection()->hasMethod($name);
    }

    public function getMethod(string $name): ?ServiceMethod
    {
        if (isset($this->methods[$name])) {
            return $this->methods[$name];
        }

        if ($this->hasMethod($name)) {
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
     * @param string $identifier The argument key.
     * @param mixed  $value      The argument value.
     * @param string $method     Optional. The argument's method.
     *
     * @return self The current service.
     */
    public function setArgument(string $identifier, $value = null, string $method = null): self
    {

        if (is_null($value) && !$this->isClass($identifier)) {
            InvalidArgumentException::identifierMustBeClass($identifier);
        }

        $value ??= $identifier;

        if ($this->isClass($identifier, $value)) {
            if (is_a($value, $identifier, true)) {
                $value = new ServiceClass($value);
            } else {
                throw new InvalidArgumentException(
                    "Class [$value] must be a subclass of [$identifier], or the same class."
                );
            }
        }

        if (is_null($method)) {
            $this->arguments[$identifier] = $value;
        } else {
            if (!$this->hasMethod($method)) {
                InvalidArgumentException::classMethodDoesNotExists($this->class, $method);
            }

            $this->getMethod($method)->setArgument($identifier, $value);
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
    public function hasArgument(string $key, string $method = null): bool
    {
        if (is_null($method)) {
            return isset($this->arguments[$key]);
        }

        if (!$this->hasMethod($method)) {
            InvalidArgumentException::classMethodDoesNotExists($this->class, $method);
        }

        return $this->getMethod($method)->hasArgument($key);
    }

    /**
     * Gets a Service argument by its key.
     *
     * @param string $key The argument key.
     *
     * @return array
     */
    public function getArgument(string $key, string $method = null)
    {
        if (is_null($method)) {
            $value = $this->arguments[$key];
        } else {
            if (!$this->hasMethod($method)) {
                InvalidArgumentException::classMethodDoesNotExists($this->class, $method);
            }

            $value =  $this->getMethod($method)->getArgument($key) ?? $this->arguments[$key];
        }

        if ($value instanceof \Closure) {
            return $value();
        }

        return $value ?? null;
    }

    /**
     * Stores the Service arguments.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return self The current service.
     */
    public function setArguments(array $arguments = [], string $method = null): self
    {
        foreach ($arguments as $key => $value) {
            $this->setArgument($key, $value, $method);
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

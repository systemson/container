<?php

namespace Amber\Container\Service;

use Amber\Validator\Validator;
use Amber\Container\Container;
use ReflectionClass;
use Amber\Container\Exception\InvalidArgumentException;

trait ArgumentsHandlerTrait
{
    /**
     * @var array The arguments for the service constructor.
     */
    protected $arguments = [];

    /**
     * @var array The parameters required for the class constructor.
     */
    protected $parameters = [];

    /**
     * Gets the method paramaters for the current class.
     *
     * @return array The parameters for the class constructor.
     */
    public function getParameters(string $method = '__construct')
    {
        if (isset($this->parameters[$method])) {
            return $this->parameters[$method];
        }

        $reflection = $this->getReflection();

        if ($reflection->hasMethod($method)) {
            $methodReflection = $reflection->getMethod($method);

            return $this->parameters[$method] = $methodReflection->getParameters() ?? [];
        }
    }

    /**
     * Stores a Service argument by its key.
     *
     * @param string $key   The argument key.
     * @param mixed  $value The argument value.
     *
     * @return self The current service.
     */
    public function setArgument(string $key, $value = null): self
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (is_null($value) && !$this->isClass($key)) {
            InvalidArgumentException::mustBeClass($key);
        }

        if (!$this->isClass($value ?? $key)) {
            $this->arguments[$key] = $value;
        } else {
            $this->arguments[$key] = new ServiceClass($value ?? $key);
        }

        return $this;
    }

    /**
     * Whether an argument is binded to the Service.
     *
     * @param string $key The argument key.
     *
     * @return bool.
     */
    public function hasArgument(string $key): bool
    {
        return isset($this->arguments[$key]);
    }

    /**
     * Gets a Service argument by its key.
     *
     * @param string $key The argument key.
     *
     * @return array.
     */
    public function getArgument(string $key)
    {
        return $this->arguments[$key];
    }

    /**
     * Stores the Service arguments.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return self The current service.
     */
    public function setArguments(array $arguments = []): self
    {
        foreach ($arguments as $key => $value) {
            $this->setArgument($key, $value);
        }

        return $this;
    }

    /**
     * Gets the Service arguments.
     *
     * @return array.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}

<?php

namespace Amber\Container\Service;

class ServiceMethod
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var \ReflectionMethod
     */
    public \ReflectionMethod $reflection;

    /**
     * @var array
     */
    protected array $arguments = [];

    /**
     * @var array
     */
    protected array $parameters = [];

    /**
     * The Service constructor.
     *
     * @param string $class The value of the service.
     */
    public function __construct(string $name, \ReflectionMethod $reflection)
    {
        $this->name = $name;
        $this->reflection = $reflection;
    }

    /**
     * Gets the method parameters.
     *
     * @return array
     */
    public function hasParameters(): bool
    {
        return $this->reflection->getNumberOfParameters() > 0;
    }

    /**
     * Gets the method parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        if (!empty($this->parameters)) {
            return $this->parameters;
        }

        return $this->parameters = $this->reflection->getParameters();
    }

    /**
     * Sets a method argument.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function setArgument(string $key, $value): self
    {
        $this->arguments[$key] = $value;

        return $this;
    }

    /**
     * Wether a method argument is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasArgument(string $key): bool
    {
        return isset($this->arguments[$key]);
    }

    /**
     * Gets a method argument.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getArgument(string $key)
    {
        return $this->arguments[$key];
    }

    /**
     * Gets all method arguments.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}

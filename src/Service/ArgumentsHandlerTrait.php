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
     * Gets the constructor paramaters for the current class.
     *
     * @return array The parameters for the class constructor.
     */
    public function getParameters(): array
    {
        if (!empty($this->parameters)) {
            return $this->parameters;
        }

        $constructor = $this->getReflection()->getConstructor();

        return $this->parameters = $constructor ? $constructor->getParameters() : [];
    }

    public function setArgument(string $key, $value): self
    {
        $this->arguments[$key] = $value;

        return $this;
    }

    public function hasArgument(string $key): bool
    {
        return isset($this->arguments[$key]);
    }

    public function getArgument(string $key)
    {
        return $this->arguments[$key];
    }

    /**
     * Gets the arguments for the Service.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return array The arguments for the class constructor.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Stores the arguments for the Service.
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
}

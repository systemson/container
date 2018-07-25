<?php

namespace Amber\Container\Service;

use Amber\Container\Exception\ContainerException;

trait DependenciesHandler
{
    /**
     *
     * @var array The arguments for the service constructor. If the service is a class.

     */
    protected $arguments = [];

    /**
     *
     * @var array The parameters required for the class constructor.
     */
    protected $parameters = [];

    /**
     *
     * @var array The injectable properties of the class.
     */
    protected $injectables = [];

    /**
     * Gets the constructor paramaters for the current class.
     *
     * @return array The parameters for the class constructor.
     */
    public function getParameters()
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        if (!empty($this->parameters)) {
            return $this->parameters;
        }

        return $this->parameters = $this->getReflection()->parameters;
    }

    /**
     * Gets the arguments for the Service.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @retun object Self instance.
     */
    public function getArguments()
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();
        return $this->arguments;
    }

    /**
     * Stores the arguments for the Service.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @retun object Self instance.
     */
    public function setArguments(array $arguments = [])
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        $this->arguments = [];

        foreach ($arguments as $argument) {
            $this->arguments[] = $argument;
        }

            return $this;
    }

    /**
     * Gets the constructor paramaters for the current class.
     *
     * @return array The parameters for the class constructor.
     */
    public function getInjectables()
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        if (empty($this->injectables)) {
            $this->injectables = $this->getReflection()->getInjectables();
        }

        return $this->injectables;
    }
}

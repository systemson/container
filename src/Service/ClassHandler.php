<?php

namespace Amber\Container\Service;

use Amber\Container\Exception\ContainerException;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Reflector;

trait ClassHandler
{
    /**
     * @var object The class reflection.
     */
    protected $reflection;

    /**
     * @var array The class public properties.
     */
    protected $properties = [];

    /**
     * @var array The class public methods.
     */
    protected $methods = [];

    /**
     * @var object The class instance.
     */
    protected $instance;

    /**
     * @var bool Singleton condition for the class.
     */
    protected $singleton = false;

    /**
     * Validates that the current Service is a class.
     *
     * @throws Amber\Container\Exception\ContainerException
     *
     * @return bool true.
     */
    protected function validateClass()
    {
        if ($this->type != 'class') {
            throw new ContainerException("Service {$this->key} is not a class.");
        }

        return true;
    }

    /**
     * Gets an instance of the Amber\Container\Reflector for the current class.
     *
     * @return object Amber\Container\Reflector instance.
     */
    protected function getReflection()
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        if (!$this->reflection instanceof Reflector) {
            $this->reflection = new Reflector($this->value);
        }

        return $this->reflection;
    }

    /**
     * Gets an array of ReflectionProperty instances for the class public properties.
     *
     * @return array
     */
    protected function getProperties()
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        if (empty($this->properties)) {
            $this->properties = $this->getReflection()->getProperties(ReflectionProperty::IS_PUBLIC);
        }

        return $this->properties;
    }

    /**
     * Gets an array of ReflectionMethod instances for the class public methods.
     *
     * @return array
     */
    protected function getMethods()
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        if (empty($this->methods)) {
            $this->methods = $this->getReflection()->getMethods(ReflectionMethod::IS_PUBLIC);
        }

        return $this->methods;
    }

    /**
     * Sets an instance for the Service.
     *
     *
     * @param array $instance The instance of the service.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return self The current service.
     */
    public function setInstance($instance)
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        if ($instance instanceof $this->value) {
            $this->instance = $instance;
            $this->singleton();

            return $this;
        }

        throw new InvalidArgumentException("Argument provided for {$this->key} is not an instance of {$this->value}");
    }

    /**
     * Instantiates the reflected class.
     *
     * @param array $arguments Optional. The arguments for the class constructor.
     *
     * @return object The instance of the reflected class
     */
    public function getInstance($arguments = [])
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        if ($this->instance instanceof $this->value) {
            return $this->instance;
        }

        $instance = $this->getReflection()->newInstance($arguments);

        if ($this->singleton == true) {
            return $this->instance = $instance;
        }

        return $instance;
    }

    /**
     * Removes the Service's instance.
     *
     * @return self The current service.
     */
    public function clear()
    {
        $this->instance = null;
        $this->singleton(false);

        return $this;
    }

    /**
     * Sets or gets the singleton property.
     *
     * @param bool $singleton The boolean value for the singleton property.
     *
     * @return object Self instance.
     */
    public function singleton($singleton = true)
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        $this->singleton = $singleton;

        return $this;
    }

    /**
     * Sets or gets the singleton property.
     *
     * @param bool $singleton The boolean value for the singleton property.
     *
     * @return object Self instance.
     */
    public function isSingleton()
    {
        /* Throws an ContainerException if the current service is not a class. */
        $this->validateClass();

        return $this->singleton;
    }
}

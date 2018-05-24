<?php

namespace Amber\Container\Service;

use Amber\Common\Validator;
use Amber\Container\Exception\ContainerException;
use Amber\Container\Reflector;

class Service
{
    use Validator;

    /**
     * @var string The name of the service.
     */
    public $key;

    /**
     * @var mixed The value of the service.
     */
    public $value;

    /**
     * @var string The value's type of the service.
     */
    public $type;

    /**
     * @var array The arguments for the service constructor. If the service is a class.
     */
    public $arguments = [];

    /**
     * @var bool Singleton condition for the class.
     */
    public $singleton = false;

    /**
     * @var object The class instance.
     */
    public $instance;

    /**
     * @var array The parameters required for the class constructor.
     */
    protected $parameters = [];

    /**
     * @var array The injectable properties of the class.
     */
    protected $injectables = [];

    /**
     * @var object The class reflection.
     */
    protected $reflection;

    /**
     * @param string $key   The name of the service.
     * @param string $Value The value of the service.
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $this->getType($value);
    }

    /**
     * Gets an instance of the Amber\Container\Reflector for the current class.
     *
     * @return object Amber\Container\Reflector instance.
     */
    protected function getReflection()
    {
        if ($this->reflection instanceof Reflector) {
            return $this->reflection;
        }

        return $this->reflection = new Reflector($this->value);
    }

    /**
     * Gets the constructor paramaters for the current class.
     *
     * @return array The parameters for the class constructor.
     */
    public function getParameters()
    {
        /* Checks if the service is a valid class. */
        if ($this->type != 'class') {
            throw new ContainerException("Service {$this->key} is not a class.");
        }

        if (!empty($this->parameters)) {
            return $this->parameters;
        }

        return $this->parameters = $this->getReflection()->parameters;
    }

    /**
     * Gets the constructor paramaters for the current class.
     *
     * @return array The parameters for the class constructor.
     */
    public function getInjectables()
    {
        /* Checks if the service is a valid class. */
        if ($this->type != 'class') {
            throw new ContainerException("Service {$this->key} is not a class.");
        }

        if (!empty($this->injectables)) {
            return $this->injectables;
        }

        return $this->injectables = $this->getReflection()->getInjectables();
    }

    /**
     * Instantiate the reflected class.
     *
     * @param array $arguments Optional. The arguments for the class constructor.
     *
     * @return object The instance of the reflected class
     */
    public function getInstance($arguments = [])
    {
        /* Checks if the service is a valid class. */
        if ($this->type != 'class') {
            throw new ContainerException("Service {$this->key} is not a class.");
        }

        if ($this->instance instanceof $this->value) {
            return $this->instance;
        }

        $instance = $this->getReflection()->newInstance($arguments);

        if ($this->singleton == true) {
            return $this->instance = $instance;
        }

        return  $instance;
    }

    /**
     * Sets or gets the singleton property.
     *
     * @param bool $singleton The boolean value for the singleton property.
     *
     * @retun object Self instance.
     */
    public function singleton($singleton = false)
    {
        /* Checks if the service is a valid class. */
        if ($this->type != 'class') {
            throw new ContainerException("Service {$this->key} is not a class.");
        }

        $this->singleton = $singleton;

        return $this;
    }

    /**
     * Sets or gets the arguments for the Service.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @retun object Self instance.
     */
    public function setArguments($arguments = [])
    {
        /* Checks if the service is a valid class. */
        if ($this->type != 'class') {
            throw new ContainerException("Service {$this->key} is not a class.");
        }

        $this->arguments = $arguments;

        return $this;
    }
}

<?php

namespace Amber\Container;

use Amber\Common\Validator;
use Amber\Container\Reflector;

class Service
{
    use Validator;

    public $value;
    public $type;
    protected $parameters;
    protected $arguments;
    protected $singleton = false;
    protected $instance;
    protected $reflection;

    public function __construct($value = null)
    {
        $this->value = $value ?? $key;
        $this->type = $this->getType($value);
    }

    public function reflection()
    {
        if ($this->reflection instanceof Reflector) {
            return $this->reflection;
        }

        return $this->reflection = new Reflector($value);
    }

    /**
     * Instantiate the reflected class.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return object The instance of the reflected class
     */
    public function instance($arguments = [])
    {
        if ($this->singleton && $this->instance instanceof $this->value) {
            return $this->instance;
        }

        if ($this->isClass($this->value)) {

            return $this->reflection()->newInstance($arguments);
        }
    }

    /**
     * Instantiate the reflected class.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return object The instance of the reflected class
     */
    public function parameters()
    {
        if ($this->parameters !== null) {
            return $this->parameters;
        }

        return $this->reflection()->parameters;
    }
}

<?php

namespace Amber\Container;

use Amber\Common\Validator;

class Service
{
    use Validator;

    public $value;
    public $type;
    public $arguments = [];
    public $is_singleton = false;
    protected $parameters;
    protected $instance;
    protected $reflection;

    public function __construct($value)
    {
        $this->value = $value;
        $this->type = $this->getType($value);
    }

    public function reflection()
    {
        if ($this->reflection instanceof Reflector) {
            return $this->reflection;
        }

        return $this->reflection = new Reflector($this->value);
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
        if ($this->instance instanceof $this->value) {
            return $this->instance;
        }

        if (!$this->isClass($this->value)) {
            return;
        }

        $instance = $this->reflection()->newInstance($arguments);

        if ($this->is_singleton) {
            return $this->instance = $instance;
        }

        return  $instance;
    }

    /**
     * Gets the constructor paramaters for the current class.
     *
     * @return object The instance of the reflected class
     */
    public function parameters()
    {
        if ($this->parameters !== null) {
            return $this->parameters;
        }

        return $this->parameters = $this->reflection()->parameters;
    }
}

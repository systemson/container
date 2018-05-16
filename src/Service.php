<?php

namespace Amber\Container;

use Amber\Common\Validator;
use Amber\Container\Reflector;

class Service
{
    use Validator;

    public $key;
    public $value;
    protected $instance;
    protected $reflection;

    public function __construct(...$arguments)
    {
        $this->key = $arguments[0];
        $this->value = $arguments[1] ?? $arguments[0];
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
        if ($this->isCallable($this->value)) {

            return $this->reflection()->newInstance($arguments);
        }
    }
}

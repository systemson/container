<?php

namespace Amber\Container\Invoker;

use Amber\Container\Injector;
use Amber\Container\Service\Service;
use Amber\Reflector\Reflector;

class ClosureClass
{
    /**
     * @var object The class to invoke.
     */
    protected $class;

    /**
     * @var object The method from the class.
     */
    protected $method;

    /**
     * @var object The instance of the class.
     */
    protected $instance;

    /**
     * The class constructor.
     *
     * @param string $class  The class to instantiate.
     * @param string $method The class method to call.
     * @param array  $args   The class constructor arguments.
     */
    public function __construct(string $callable, array $args = [])
    {
        $array = explode('@', $callable);

        $class = $array[0];
        $method = $array[1] ?? null;

        $this->class = $class;
        $this->constuct_arguments = $args;
        $this->method = $method;
    }

    /**
     * Invokes the class method.
     *
     * @todo Test classes without methods, called by the __invoke magic method.
     *
     * @param array $args The arguments for the called method.
     *
     * @return mixed The result from the method.
     */
    public function __invoke(...$args)
    {
        return call_user_func_array([$this->getInstance(), $this->method], $args);
    }

    /**
     * Instantiates or gets the instance the class.
     *
     * @param array $arguments Optional. The arguments for the class constructor.
     *
     * @return object The instance of the class
     */
    protected function getInstance(array $args = [])
    {
        if (!$this->instance instanceOf $this->class) {
            $this->instance = (new Service($this->class, $this->class))->getInstance($this->constuct_arguments);
        }

        return $this->instance;
    }
}

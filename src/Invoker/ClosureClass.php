<?php

namespace Amber\Container\Invoker;

use Amber\Container\Injector;
use Amber\Container\Service\Service;

class ClosureClass
{
    /**
     * @var object The instance of the class.
     */
    protected $instance;

    /**
     * @var object The method from the class.
     */
    protected $method;

    /**
     * The class constructor.
     *
     * @param string $class  The class to instantiate.
     * @param string $method The class method to call.
     * @param array  $args   The class constructor arguments.
     */
    public function __construct($callable, $args = [])
    {
        $array = explode('@', $callable);

        $class = $array[0];
        $method = $array[1] ?? null;

        $this->instance = (new Service($class, $class))->getInstance($args);
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
        return call_user_func_array([$this->instance, $this->method], $args);
    }
}

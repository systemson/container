<?php

namespace Amber\Container;

use Amber\Container\Service\ClosureClass;

/**
 * Wrapper class to obtain a callable object from a specified class and method.
 *
 * @todo Should be moved to it's own package.
 */
class Invoker
{
    /**
     * @var The class method to call.
     */
    protected $class;

    /**
     * @var The class method to call.
     */
    protected $method;

    /**
     * @var The class constructor arguments.
     */
    protected $arguments = [];

    /**
     * @var object The instance of the Amber\Container\Injector.
     */
    protected $container;

    /**
     * @var array
     */
    protected $before = [];

    /**
     * @var array
     */
    protected $after = [];

    /**
     * The invoker constructor.
     */
    public function __construct()
    {
        $this->container = new Injector();
        $this->container->setConfig(['cache' => [
            'cache_driver' => 'array'
        ]]);
    }

    /**
     * Sets the class.
     *
     * @param string $class The class.
     *
     * @return static
     */
    public function from($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Sets the class constructor arguments.
     *
     * @param mixed|array $args The class constructor arguments.
     *
     * @return static
     */
    public function buildWith(...$args)
    {
        $this->arguments = call_user_func_array('array_merge', $args);

        return $this;
    }

    /**
     * Sets the class method to call.
     *
     * @param string $method The class method.
     *
     * @return static
     */
    public function call($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Sets the method to call before the main action is called.
     *
     * @param string $method The method to call before the main action.
     * @param array  $args   Optional. The arguments to pass to the method.
     *
     * @return static
     */
    public function before($method, ...$args)
    {
        $this->before = (object) [
            'method' => $method,
            'arguments' => $args ?? [],
        ];

        return $this;
    }

    /**
     * Sets the method to call after the main action is called.
     *
     * @param string $method The method to call before the main action.
     * @param array  $args   Optional. The arguments to pass to the method.
     *
     * @return static
     */
    public function after($method, ...$args)
    {
        $this->after = (object) [
            'method' => $method,
            'arguments' => $args ?? [],
        ];

        return $this;
    }

    /**
     * Calls the class method and pass the arguments.
     *
     * @param mixed|array $args The class method arguments.
     *
     * @return static
     */
    public function do(...$args)
    {
        $closure = $this->getClosure(
            $this->class . '@' . $this->method,
            $this->container->bindAndGetMultiple($this->arguments)
        );

        if (isset($this->before->method)) {
            $closure->setBeforeAction($this->before->method, $this->before->arguments);
        }

        if (isset($this->after->method)) {
            $closure->setAfterAction($this->after->method, $this->after->arguments);
        }

        return $closure($args);
    }

    /**
     * Returns the class method as an argument.
     *
     * @param string $class  The class to instantiate.
     * @param string $method The class method to call.
     * @param array  $args   The class constructor arguments.
     *
     * @return ClosureClass
     */
    public static function getClosure($callable, $args = [])
    {
        return new ClosureClass($callable, $args);
    }
}

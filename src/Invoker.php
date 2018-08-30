<?php

namespace Amber\Container;

use Amber\Container\Invoker\ClosureClass;

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
     * The invoker constructor.
     */
    public function __construct()
    {
        $this->container = new Injector();
        $this->container->setConfig(['cache_driver', 'array']);
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
    public function with(...$args)
    {
        $this->arguments = $args;

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
     * Calls the class method and pass the arguments.
     *
     * @param mixed|array $args The class method arguments.
     *
     * @return static
     */
    public function do(...$args)
    {
        $closure = $this->getClosure(
            $this->class,
            $this->method,
            $this->container->bindAndGetMultiple($this->arguments)
        );

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
    public static function getClosure($class, $method, $args = [])
    {
        return new ClosureClass($class, $method, $args);
    }
}

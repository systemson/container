<?php

namespace Amber\Container\Service;

use Amber\Container\Injector;
use Amber\Container\Service\Service;
use Amber\Reflector\Reflector;

/**
 * This class builds a closure for calling a specified method from a class.
 */
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
     * @var array The method to invoke before the main action.
     */
    protected $before;

    /**
     * @var array The method to invoke after the main action.
     */
    protected $after;

    /**
     * The class constructor.
     *
     * @param string $callable  The class and method to instantiate in the format "ThisClass@thisMethod".
     * @param array  $args      The class constructor arguments.
     */
    public function __construct(string $callable, array $args = [])
    {
        $array = explode('::', $callable);

        $class = $array[0];
        $method = $array[1] ?? null;

        $this->class = $class;
        $this->constuct_arguments = $args;
        $this->method = $method;
    }

    /**
     * Invokes the class method.
     *
     * @todo Test classes called by it's own __invoke magic method.
     *
     * @param array $args The arguments for the called method.
     *
     * @return mixed The result from the method.
     */
    public function __invoke(...$args)
    {
        $this->beforeAction();
        $return = call_user_func_array([$this->getInstance(), $this->method], $args);
        $this->afterAction();

        return $return;
    }

    /**
     * Instantiates or gets the instance the class.
     *
     * @param array $args Optional. The arguments for the class constructor.
     *
     * @return object The instance of the class
     */
    protected function getInstance(array $args = [])
    {
        if (!$this->instance instanceof $this->class) {
            $this->instance = (new ServiceClass($this->class, $this->class))->getInstance($this->constuct_arguments);
        }

        return $this->instance;
    }

    /**
     * Sets the method to call before the main action is called.
     *
     * @todo MUST validate that this method is callable from the class.
     *
     * @param string $method    The method to call before the main action.
     * @param array  $arguments Optional. The arguments to pass to the method.
     *
     * @return void
     */
    public function setBeforeAction($method, array $arguments = [])
    {
        $this->before = (object) [
            'method' => $method,
            'arguments' => $arguments,
        ];
    }

    /**
     * Calls the specified method before the main action is called.
     *
     * @todo Should work for calling a method from another class.
     *
     * @return void
     */
    protected function beforeAction()
    {
        if (isset($this->before->method)) {
            call_user_func_array([$this->getInstance(), $this->before->method], $this->before->arguments);
        }
    }

    /**
     * Sets the method to call after the main action is called.
     *
     * @todo MUST validate that this method is callable from the class.
     *
     * @param string $method    The method to call before the main action.
     * @param array  $arguments Optional. The arguments to pass to the method.
     *
     * @return void
     */
    public function setAfterAction($method, array $arguments = [])
    {
        $this->after = (object) [
            'method' => $method,
            'arguments' => $arguments,
        ];
    }

    /**
     * Calls the specified method after the main action is called.
     *
     * @todo Should work for calling a method from another class.
     *
     * @return void
     */
    protected function afterAction()
    {
        if (isset($this->after->method)) {
            call_user_func_array([$this->getInstance(), $this->after->method], $this->after->arguments);
        }
    }
}

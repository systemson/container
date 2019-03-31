<?php

namespace Amber\Container\Service;

use Amber\Validator\Validator;
use Amber\Container\Container;
use ReflectionClass;
use Amber\Container\Exception\InvalidArgumentException;

class ServiceClass
{
    use Validator, ArgumentsHandlerTrait;

    /**
     * @var string The class name.
     */
    public $class;

    /**
     * @var mixed The class instance.
     */
    protected $instance;

    /**
     * @var ReflectionClass The class reflection.
     */
    protected $reflection;

    /**
     * @var bool Singleton condition for the class.
     */
    protected $singleton = false;

    /**
     * @var array
     */
    protected $callback = [];

    /**
     * The Service constructor.
     *
     * @param string $class The value of the service.
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * Gets an instance of the ReflectionClass for the current class.
     *
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass
    {
        if (!$this->reflection instanceof ReflectionClass) {
            $this->reflection = new ReflectionClass($this->class);
        }

        return $this->reflection;
    }

    /**
     * Sets an instance for the Service.
     *
     * @param object $instance The instance of the service.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return self The current service.
     */
    public function setInstance($instance): self
    {
        if (!$instance instanceof $this->class) {
            InvalidArgumentException::mustBeInstanceOf($this->class);
        }

        $this->instance = $instance;
        $this->singleton();

        return $this;
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
        if ($this->instance instanceof $this->class) {
            return $this->instance;
        }

        $instance = $this->new($arguments);

        if ($this->isSingleton()) {
            return $this->instance = $instance;
        }

        return $instance;
    }

    /**
     * Instantiates the reflected class.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return object The instance of the reflected class
     */
    protected function new($arguments = [])
    {
        if (!empty($arguments)) {
            $instance = $this->getReflection()->newInstanceArgs($arguments);
        } else {
            $instance = $this->getReflection()->newInstance();
        }

        foreach ($this->callback as $method) {
            $args = $method->args;
            
            if ($callback = $args[0] instanceof \Closure) {
                $args[0] = $args[0]();
            }
            call_user_func_array([$instance, $method->name], $args);
        }

        return $instance;
    }

    /**
     * Removes the Service's instance.
     *
     * @return self The current service.
     */
    public function clear(): self
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
     * @return self The current service.
     */
    public function singleton(bool $singleton = true): self
    {
        $this->singleton = $singleton;

        return $this;
    }

    /**
     * Whether the class is singleton.
     *
     * @return bool.
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * Method to call after the class is build
     *
     * @param string $method The class method to call.
     * @param array  $args   The arguments for the method.
     *
     * @return self
     */
    public function afterConstruct(string $method, ...$args): self
    {
        $methods = get_class_methods($this->class);

        if (!in_array($method, $methods)) {
            throw new \Exception("Method \"{$method}\" does not exists.", 1);
        }

        $this->callback[] = (object) [
            'name' => $method,
            'args' => $args,
        ];

        return $this;
    }
}

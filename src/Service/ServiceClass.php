<?php

namespace Amber\Container\Service;

use Amber\Validator\Validator;
use Amber\Container\Container;
use ReflectionClass;
use Amber\Container\Exception\InvalidArgumentException;

class ServiceClass implements \ArrayAccess
{
    use Validator;

    /**
     * @var string The class name.
     */
    public $class;

    /**
     * @var Container
     */
    public $container;

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
     * @var array The arguments for the service constructor.
     */
    protected $arguments = [];

    /**
     * @var array The parameters required for the class constructor.
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $call = [];

    /**
     * The Service constructor.
     *
     * @param string $class The value of the service.
     */
    public function __construct(string $class, Container $container)
    {
        $this->class = $class;
        $this->container = $container;
    }

    /**
     * Gets an instance of the ReflectionClass for the current class.
     *
     * @return ReflectionClass
     */
    protected function getReflection(): ReflectionClass
    {
        if (!$this->reflection instanceof ReflectionClass) {
            $this->reflection = new ReflectionClass($this->class);
        }

        return $this->reflection;
    }

    /**
     * Sets an instance for the Service.
     *
     * @param array $instance The instance of the service.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return self The current service.
     */
    public function setInstance($instance): self
    {
        if ($instance instanceof $this->class) {
            $this->instance = $instance;
            $this->singleton();

            return $this;
        }

        throw new InvalidArgumentException("Argument provided is not an instance of {$this->class}");
    }

    /**
     * Instantiates the reflected class.
     *
     * @param array $arguments Optional. The arguments for the class constructor.
     *
     * @return mixed The instance of the reflected class
     */
    public function getInstance($arguments = [])
    {
        if ($this->instance instanceof $this->class) {
            return $this->instance;
        }

        $instance = $this->newInstance($arguments);

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
     * @return mixed The instance of the reflected class
     */
    public function newInstance($arguments = [])
    {
        if (!empty($arguments)) {
            //$arguments = array_map(function ($value) {return array_values($value)[0];}, $arguments));
            $instance = $this->getReflection()->newInstanceArgs(array_values($arguments));
        } else {
            $instance = $this->getReflection()->newInstance();
        }

        foreach ($this->call as $method) {
            $this->callback($method->name, $method->args);
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
     * Gets the constructor paramaters for the current class.
     *
     * @return array The parameters for the class constructor.
     */
    public function getParameters(): array
    {
        if (!empty($this->parameters)) {
            return $this->parameters;
        }

        $constructor = $this->getReflection()->getConstructor();

        return $this->parameters = $constructor ? $constructor->getParameters() : [];
    }

    /**
     * Gets the arguments for the Service.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return array The arguments for the class constructor.
     */
    public function getArguments(): array
    {
        return $this->container->getCollection()->get("{$this->class}_arguments") ?? [];
    }

    /**
     * Stores the arguments for the Service.
     *
     * @param array $arguments The arguments for the class constructor.
     *
     * @return self The current service.
     */
    public function setArguments(array $arguments = []): self
    {
        foreach ($arguments as $key => $value) {
            $this->container->getCollection()->pushTo("{$this->class}_arguments", [
                $key => $value
            ]);
        }

        return $this;
    }

    public function hasArgument(string $key, string $type = null): bool
    {
        $args = $this->getArguments();

        return isset($args[$key]);
    }

    public function getArgument(string $key): bool
    {
        $args = $this->getArguments();

        return $args[$key];
    }

    public function afterConstruct(string $method, ...$args)
    {
        $methods = get_class_methods($this);

        if (in_array($method, $methods)) {
            $this->callback[] = (object) [
                'name' => $method,
                'arguments' => $args,
            ];
        }

        // Must throw an exception
    }

    public function call($method, $args)
    {
        if (get_class_methods($this)) {
            return call_user_func_array([$this, $method], $args);
        }

        // Must throw an exception
    }

    public function offsetExists($key)
    {
        $props = $this->toArray();

        return isset($props[$key]);
    }

    public function offsetGet($key)
    {
        $props = $this->toArray();

        return $props[$key] ?? null;
    }

    public function offsetSet($key, $value)
    {
        if ($this->offsetExists($key)) {
            $this->{$key} = $value;
        }
    }

    public function offsetUnset($key)
    {
        $this->offsetSet($key, null);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

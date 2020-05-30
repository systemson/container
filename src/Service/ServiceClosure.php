<?php

namespace Amber\Container\Service;

use Amber\Container\Container;
use Opis\Closure\SerializableClosure;
use Closure;

class ServiceClosure
{
    /**
     * @var Closure
     */
    protected $callback;

    /**
     * @var \ReflectionFunction.
     */
    protected $reflection;

    /**
     * The Service constructor.
     *
     * @param string $class The value of the service.
     */
    public function __construct(Closure $callback)
    {
        $this->callback = new SerializableClosure($callback);
    }

    /**
     * Invokes the service callback.
     *
     * @param mixed $args
     *
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return call_user_func_array([$this->callback, '__invoke'], $args);
    }

    /**
     * Gets an instance of the ReflectionFunction for the current closure.
     *
     * @return \ReflectionFunction
     */
    public function getReflection(): \ReflectionFunction
    {
        if ($this->reflection instanceof \ReflectionFunction) {
            return $this->reflection;
        }

        return $this->reflection = new \ReflectionFunction($this->callback->getClosure());
    }

    /**
     * Gets the closure parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->getReflection()->getParameters();
    }

    public function getName()
    {
        return $this->getReflection()->getName();
    }
}

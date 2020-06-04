<?php

namespace Amber\Container\Service;

use Amber\Container\Container;
use Opis\Closure\SerializableClosure;
use Closure;
use Amber\Container\Contracts\ServiceInterface;

class ServiceClosure implements ServiceInterface
{
    /**
     * @var SerializableClosure
     */
    protected $callback;

    /**
     * @var \Reflector
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
     * @return \Reflector
     */
    public function getReflection(): \Reflector
    {
        if ($this->reflection instanceof \ReflectionFunction) {
            return $this->reflection;
        }

        return $this->reflection = new \ReflectionFunction($this->callback->getClosure());
    }

    /**
     * Gets the Service arguments.
     *
     * @param string $method The service's method.
     *
     * @return array
     */
    public function getParameters(string $method = '__invoke'): array
    {
        return $this->getReflection()->getParameters();
    }

    /**
     * Gets the full namespace of the service.
     *
     * @return string The service's name.
     */
    public function getName()
    {
        return $this->getReflection()->getName();
    }

    /**
     * Gets the wrapped callback.
     *
     * @return Closure
     */
    public function getClosure(): Closure
    {
        return $this->callback->getClosure();
    }
}

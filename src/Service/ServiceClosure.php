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
}

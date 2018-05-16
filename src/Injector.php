<?php

namespace Amber\Container;

use Amber\Cache\Cache;
use Amber\Container\Binder\Binder;

class Injector extends Binder
{
    /**
     * @var Cache driver.
     */
    public $cacher;

    public function __construct()
    {
    }

    /**
     * Get an instance of the specified class.
     *
     * @param string $class     The class to be instantiated.
     * @param array  $arguments Optional. The arguments for the constructor.
     *
     * @throws Amber\Container\ContainerException
     *
     * @return object The instance of the class
     */
    public function getInstanceOf(string $class, array $arguments = [])
    {
        /* Check if the class exists */
        if (!class_exists($class)) {
            throw new ContainerException("Class {$class} does not exists.");
        }

        /* Check the instance of the class is in the cache */
        if (Cache::has($class)) {
            return Cache::get($class);
        }

        /* Get the class reflection */
        $reflector = new Reflector($class);

        /* Get class constructor arguments */
        $arguments = $this->getArguments($reflector->parameters, $arguments);

        /* Instantiate the class */
        $instance = $reflector->newInstance($arguments);

        /* Inject dependencies */
        $instance = $this->inject($instance, $reflector->injectables);

        if ($instance instanceof $class) {
            Cache::set($class, $instance, 15);

            return $instance;
        }
    }
}

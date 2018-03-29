<?php

namespace Amber\Container;

use Amber\Cache\Cache;

class Container
{
    use Binder;

    protected $instance;

    public $map = [];

    /**
     * Get an instance of the class.
     *
     * @param string $class     The class to be instantiated.
     * @param array  $arguments Optional. The arguments for the constructor.
     *
     * @throws Amber\Container\ContainerException
     *
     * @return object The instance of the class
     */
    public function getInstanceOf(string $name, array $arguments = [])
    {
        /* Check if the class exists */
        if (!class_exists($name)) {
            throw new ContainerException("DI Container: class {$class} does not exists.");
        }

        /* Check the instance of the class is in the cache */
        if (Cache::has($name)) {
            return Cache::get($name);
        }

        /* Get the class reflection */
        $class = new Reflector($name);

        /* Get class constructor arguments */
        $arguments = $this->getArguments($class->parameters, $arguments);

        /* Instantiate the class */
        $instance = $class->newInstance($arguments);

        /* Inject dependencies */
        $instance = $this->inject($instance, $class->injectables);

        if ($instance instanceof $name) {
            Cache::set($name, $instance, 15);

            return $instance;
        }
    }
}

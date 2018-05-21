<?php

namespace Amber\Container;

use Amber\Cache\Cache;
use Amber\Container\Container\Binder;
use Amber\Container\Container\Pusher;
use Amber\Container\Exception\InvalidArgumentException;

class Injector extends Binder
{
    use Pusher;

    /**
     * @var Container's configuration.
     */
    public $config;

    /**
     * @var Cache driver.
     */
    public $cacher;

    const CACHE_DRIVER = 'file';

    public function __construct($config = [])
    {
        $this->config = $config;
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
    public function mount(string $class, array $arguments = [])
    {
        /* Check if the class exists */
        if (!$this->isClass($class)) {
            throw new InvalidArgumentException("Class argument {$class} is not a valid class or do not exists.");
        }

        /* Check the instance of the class is in the cache */
        if (Cache::has($class)) {
            return Cache::get($class);
        }

        /* Instantiate the class */
        $instance = $this->get($class);

        Cache::set($class, $instance, 15);

        return $instance;
    }
}

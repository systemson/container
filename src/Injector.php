<?php

namespace Amber\Container;

use Amber\Container\Config\ConfigAwareInterface;
use Amber\Container\Config\ConfigAwareTrait;
use Amber\Container\Container\Binder;
use Amber\Container\Container\CacheHandler;
use Amber\Container\Container\Pusher;
use Amber\Container\Exception\InvalidArgumentException;

class Injector extends Binder implements ConfigAwareInterface
{
    use Pusher, ConfigAwareTrait, CacheHandler;

    /**
     * The Injector constructor.
     *
     * @param array $config The configurations for the Container.
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Gets an instance of the specified class, and stores it.
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
        /* Checks if the class exists */
        if (!$this->isClass($class)) {
            throw new InvalidArgumentException("Class {$class} is not a valid class or do not exists.");
        }

        /* Checks if the instance of the class is in the cache */
        if ($this->getCache()->has($class)) {
            return $this->getCache()->get($class);
        }

        $service = $this->findAndBind($class)->singleton(true);

        /* Gets an instance of the class */
        $instance = $this->instantiate($service, $arguments);

        $this->getCache()->set($class, $instance, 15);

        return $instance;
    }
}

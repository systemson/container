<?php

namespace Amber\Container;

use Amber\Container\Container\Binder;
use Amber\Container\Container\CacheHandler;
use Amber\Container\Container\Pusher;
use Amber\Container\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Amber\Collection\CollectionAware\CollectionAwareInterface;
use Amber\Collection\CollectionAware\CollectionAwareTrait;
use Amber\Container\Config\ConfigAwareTrait;
use Amber\Container\Config\ConfigAwareInterface;

/**
 * Handles the dependency injection.
 *
 * @todo Load class config data for the instantiation.
 * @todo Load and bind dependencies from config.
 * @todo This class should validate for a ClassAwareInterface to inject depedencies.
 */
class Injector extends Binder implements ContainerInterface, ConfigAwareInterface, CollectionAwareInterface
{
    use Pusher, CacheHandler, ConfigAwareTrait, CollectionAwareTrait;

    /**
     * The Container constructor.
     *
     * @param array $config The configurations for the Container.
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
        $this->initCollection();

        $this->bindMultiple($this->getConfig('container.services', []));
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

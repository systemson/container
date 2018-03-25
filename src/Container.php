<?php

namespace Amber\Container;

use Amber\Cache\Cache;

class Container
{
    use MapKeyTrait, ReflectionTrait;

    protected static $instance;

    public static $map = [];

    final private function __construct()
    {
    }

    final private function __clone()
    {
    }

    final private function __wakeup()
    {
    }

    /**
     * Singleton implementation.
     */
    public static function getInstance()
    {
        /* Checks if the Container is already instantiated. */
        if (!self::$instance instanceof self) {
            /* Instantiate the Container class */
            self::$instance = new self();
        }

        /* Return the instance of Container */
        return self::$instance;
    }

    public static function getInstanceOf(string $className, array $arguments = [])
    {
        /* Check the instance of the class is in the cache */
        if (Cache::has($className)) {
            //return Cache::get($className);
        }

        /* Check if the class exists */
        if (!class_exists($className)) {
            throw new ContainerException("DI Container: class {$className} do not exists.");
        }

        /* Instantiate the ReflectionClass */
        $reflection = self::reflectionOf($className);

        /* Get the constructor parameters */
        $classParams = self::getMethodParams($reflection->getConstructor());

        if (!empty($classParams)) {
            $params = !empty($params) ? $params : self::getParametersFromMap($classParams);

            /* Create an instance of the class */
            $instance = $reflection->newInstanceArgs($params);
        } else {
            $instance = $reflection->newInstance();
        }

        $injectables = self::getInjectableProperties($reflection);

        $instance = self::inject($instance, $injectables);

        if ($instance instanceof $className) {
            Cache::set($className, $instance, 15);

            return $instance;
        }

        throw new ContainerException("DI Container: class {$className} could not be instantiated.");
    }
}

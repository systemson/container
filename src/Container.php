<?php

namespace Amber\Container;

use Amber\Cache\Cache;

class Container
{
    protected static $instance;
    public static $map = [];

    final private function __construct () {}
    final private function __clone() {}
    final private function __wakeup() {}

    /**
     * Singleton implementation.
     */
    public static function getInstance()
    {
        /** Checks if the Container is already instantiated. */
        if (!self::$instance instanceof self)
        {
            /** Instantiate the Container class */
            self::$instance = new Container();
        }

        /** Return the instance of Container */
        return self::$instance;
    }

    public static function set(string $key, string $obj)
    {
        if(empty(self::$map)) {
            self::$map = (object) array();
        }
        self::$map->{$key} = $obj;
    }

    public static function get(string $key = null)
    {
        return self::$map->{$key} ?? null;
    }

    public static function getInstanceOf(string $className, array $arguments = [])
    {
        if(Cache::has($className)) {
            return Cache::get($className);
        }

        // checking if the class exists
        if(!class_exists($className)) {
            throw new ContainerException("DI: missing class '".$className."'.");
        }

        // Instantiate the ReflectionClass
        $reflection = new \ReflectionClass($className);

        // Get the constructor parameters
        $classParams = self::getMethodParameters($reflection->getConstructor());

        if (!empty($classParams)) {
            $arguments = empty($arguments) ? self::getParametersFromMap($classParams) : $arguments;

            // creating an instance of the class
            $obj = $reflection->newInstanceArgs($arguments);
        } else {

            $obj = $reflection->newInstance();
        }


        if (Cache::set($className, $obj, 15)) {
            return $obj;
        }
    }

    public static function getMethodParameters($method)
    {
        if($method instanceof \ReflectionMethod) {
            return $method->getParameters();
        }
        return null;
    }

    public static function getParametersFromMap(array $keys = [])
    {
        foreach ($keys as $key) {
            if (class_exists($key->name)) {
                $params[] = self::getInstanceOf($key->name);
            } elseif (is_string($key->name)) {
                $params[] = self::get($key->name);
            }
        }

        return $params;
    }
}

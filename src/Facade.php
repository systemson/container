<?php

namespace Amber\Container;

/**
 * @todo Should be moved to it's own package.
 */
abstract class Facade
{
    /**
     * @var The DI container.
     */
    protected static $container;

    /**
     * @var The class instance of the class to call.
     */
    protected static $instance;

    /**
     * @var The class accessor.
     */
    protected static $accessor;

    public static function setContainer($container)
    {
        static::$container = $container;
    }

    public static function getContainer()
    {
        return static::$container;
    }

    public static function setAccessor($accessor)
    {
        static::$accesor = $accessor;
    }

    public static function getAccessor()
    {
        return static::$accessor;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getContainer()->get(static::getAccesor());

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return call_user_func_array([$instance, $method], $args);
    }
}

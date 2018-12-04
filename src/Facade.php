<?php

namespace Amber\Container;

use RuntimeException;

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
     * @var The class accessor.
     */
    protected static $accessor;

    /**
     * @var The instance of the class.
     */
    protected static $instance;

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

    public static function root()
    {
        return static::getContainer()->get(static::getAccesor());
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
        $instance = self::root();

        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return call_user_func_array([$instance, $method], $args);
    }
}

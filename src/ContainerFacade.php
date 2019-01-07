<?php

namespace Amber\Container;

use RuntimeException;
use Amber\Utils\Implementations\AbstractWrapper;

/**
 * @todo Should be moved to it's own package.
 */
abstract class ContainerFacade extends AbstractWrapper
{
    /**
     * @todo MUST be moved to a ContainerAwareTrait
     *
     * @var The DI container.
     */
    protected static $container;

    /**
     * @todo MUST be moved to a ContainerAwareTrait
     */
    public static function setContainer($container)
    {
        static::$container = $container;
    }

    /**
     * @todo MUST be moved to a ContainerAwareTrait
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * Returns the instance of the class.
     */
    public static function getInstance()
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
    public static function __callStatic($method, $args = [])
    {
        $instance = self::getInstance();

        if (!$instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return call_user_func_array([$instance, $method], $args);
    }
}

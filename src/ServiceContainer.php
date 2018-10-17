<?php

namespace Amber\Container;

use Amber\Container\Container\SimpleBinder;
use Amber\Utils\Implementations\AbstractSingleton;

/**
 * @deprecated
 */
final class ServiceContainer extends AbstractSingleton
{
    /**
     * Get the instance of the ServiceContainer class.
     *
     * @return object ServiceContainer Instance.
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof SimpleBinder) {
            self::$instance = new SimpleBinder();
        }

        return self::$instance;
    }
}

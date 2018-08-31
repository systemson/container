<?php

namespace Amber\Container;

use Amber\Container\Container\SimpleBinder;

final class ServiceContainer extends SimpleBinder
{
    /**
     *
     * @var object ServiceContainer Instance.
     */
    private static $instance;

    /**
     * Get the instance of the ServiceContainer class.
     *
     * @return object ServiceContainer Instance.
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

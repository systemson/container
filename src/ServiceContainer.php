<?php

namespace Amber\Container;

use Amber\Container\Container\Binder;

class ServiceContainer extends Binder
{
    /**
     * @var object ServiceContainer Instance.
     */
    private static $instance;

    /**
     * Set private to prevent instantiation.
     */
    private function __construct()
    {
    }

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

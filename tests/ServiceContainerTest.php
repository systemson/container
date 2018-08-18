<?php

namespace Tests;

use Amber\Container\Container\SimpleBinder;
use Amber\Container\ServiceContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ServiceContainerTest extends TestCase
{
    public function testServiceContainer()
    {
        $container = ServiceContainer::getInstance();

        /* Checks if the Container returns an instance of ServiceContainer */
        $this->assertInstanceOf(
            ServiceContainer::class,
            $container->getInstance()
        );

        /* Checks if the Container returns an instance of Binder */
        $this->assertInstanceOf(
            ContainerInterface::class,
            $example = $container->getInstance()
        );

        /* Checks if the Container returns an instance of Binder */
        $this->assertInstanceOf(
            SimpleBinder::class,
            $example = $container->getInstance()
        );
    }
}

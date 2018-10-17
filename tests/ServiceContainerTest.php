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

        /* Checks if the Container returns an instance of Psr\Container\ContainerInterface */
        $this->assertInstanceOf(
            ContainerInterface::class,
            $container
        );

        /* Checks if the Container returns an instance of SimpleBinder */
        $this->assertInstanceOf(
            SimpleBinder::class,
            $container
        );
    }
}

<?php

namespace Amber\Container\Tests;

use Amber\Container\ServiceContainer;
use Amber\Container\Binder;
use PHPUnit\Framework\TestCase;

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
            Binder::class,
            $example = $container->getInstance()
        );
    }
}

<?php

namespace Tests;

use Amber\Container\Injector;
use Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class CacheHandlerTest extends TestCase
{
    public function testCache()
    {
        $container = new Injector();

        /* Variables */
        $key = 'key';
        $string = 'string';
        $number = 1;
        $array = [1, 2, 3, 4, 5];
        $class = Model::class;
        $object = new $class();

        /* Test pick() and drop() */
        $this->assertTrue($container->bind($key . '1', $string));
        $this->assertTrue($container->bind($key . '2', $string));
        $this->assertTrue($container->bind($key . '3', $string));
        $this->assertTrue($container->bind($class));

        /* Stores the services in the cache */
        $this->assertTrue($container->drop());

        /* Cleares the services and checks that no keys exists */
        $this->assertTrue($container->clear());
        $this->assertTrue($container->isEmpty());
        $this->assertFalse($container->has($key . '1'));
        $this->assertFalse($container->has($key . '2'));
        $this->assertFalse($container->has($key . '3'));
        $this->assertFalse($container->has($class));

        /* Load the services from the cache */
        $this->assertTrue($container->pick());

        /* Tests that the services are being restored from the cache */
        $this->assertFalse($container->isEmpty());
        $this->assertTrue($container->has($key . '1'));
        $this->assertTrue($container->has($key . '2'));
        $this->assertTrue($container->has($key . '3'));
        $this->assertTrue($container->has($class));

        $container->clear(true);
    }
}

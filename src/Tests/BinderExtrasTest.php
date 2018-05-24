<?php

namespace Amber\Container\Tests;

use Amber\Container\Injector;
use Amber\Container\Tests\Example\ChildModel;
use Amber\Container\Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class BinderExtrasTest extends TestCase
{
    public function testExtras()
    {
        $container = new Injector();

        /* Variables */
        $key = 'key';
        $string = 'string';
        $number = 1;
        $array = [1, 2, 3, 4, 5];
        $class = Model::class;
        $object = new $class();

        /* Test pull() */
        $this->assertTrue($container->bind($key, $class));
        $this->assertEquals($object, $container->pull($key));
        $this->assertFalse($container->has($key));

        /* Test put() */
        $this->assertTrue($container->put($key, $class));
        $this->assertEquals($object, $container->get($key));
        $this->assertTrue($container->unbind($key));

        /* Test set() */
        $this->assertTrue($container->bind($key, $class));
        $this->assertTrue($container->set($key, $object));
        $this->assertSame($object, $container->get($key));
        $this->assertTrue($container->unbind($key));

        /* Test update() */
        $this->assertTrue($container->bind($key, $class));
        $this->assertTrue($container->update($key, ChildModel::class));
        $this->assertEquals(new ChildModel(), $container->get($key));
        $this->assertTrue($container->unbind($key));

        /* Test clear() */
        $this->assertTrue($container->bind($key.'1', $string));
        $this->assertTrue($container->bind($key.'2', $number));
        $this->assertTrue($container->bind($key.'3', $array));
        $this->assertFalse($container->isEmpty());
        $this->assertSame(3, $container->count());
        $this->assertTrue($container->clear());
        $this->assertTrue($container->isEmpty());

        /* Test init() and build() */
        $this->assertTrue($container->bind($key.'1', $string));
        $this->assertTrue($container->bind($key.'2', $string));
        $this->assertTrue($container->bind($key.'3', $string));

        /* Stores the services in the cache */
        $container->build();

        /* Cleares the services and checks that no keys exists */
        $this->assertTrue($container->clear());
        $this->assertTrue($container->isEmpty());
        $this->assertFalse($container->has($key.'1'));
        $this->assertFalse($container->has($key.'2'));
        $this->assertFalse($container->has($key.'3'));

        /* Load the services from the cache */
        $container->init();

        /* Tests that the services are being restored from the cache */
        $this->assertFalse($container->isEmpty());
        $this->assertTrue($container->has($key.'1'));
        $this->assertTrue($container->has($key.'2'));
        $this->assertTrue($container->has($key.'3'));
    }
}

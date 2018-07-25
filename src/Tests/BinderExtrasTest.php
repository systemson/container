<?php

namespace Amber\Container\Tests;

use Amber\Container\Exception\ContainerException;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
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
        $this->assertTrue($container->bind($key . '1', $string));
        $this->assertTrue($container->bind($key . '2', $number));
        $this->assertTrue($container->bind($key . '3', $array));
        $this->assertFalse($container->isEmpty());
        $this->assertSame(3, $container->count());
        $this->assertTrue($container->clear());
        $this->assertTrue($container->isEmpty());

        return $container;
    }

    /**
     *
     * @depends testExtras
     */
    public function testPutException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        $container->put(1, 'string');
    }

    /**
     *
     * @depends testExtras
     */
    public function testSetNotFoundException($container)
    {
        $this->expectException(NotFoundException::class);

        $container->set('test', new Model());
    }

    /**
     *
     * @depends testExtras
     */
    public function testSetContainerException($container)
    {
        $this->expectException(ContainerException::class);

        $container->bind('test', 'test');
        $container->set('test', new Model());
    }

    /**
     *
     * @depends testExtras
     */
    public function testSetInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        $container->bind(Model::class);
        $container->set(Model::class, 'string');
    }

    /**
     *
     * @depends testExtras
     */
    public function testUpdateInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        $container->update(1, 'string');
    }

    /**
     *
     * @depends testExtras
     */
    public function testUpdateNotFoundException($container)
    {
        $this->expectException(NotFoundException::class);

        $container->update('string', 'string');
    }
}

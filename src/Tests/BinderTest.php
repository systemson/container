<?php

namespace Amber\Container\Tests;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Injector;
use Amber\Container\Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class BinderTest extends TestCase
{
    public function testBinder()
    {
        $container = new Injector();

        /* Variables */
        $key = 'key';
        $string = 'string';
        $number = 1;
        $array = [1, 2, 3, 4, 5];
        $class = Model::class;
        $object = new $class();

        /* Test strings */
        $this->assertTrue($container->bind($key, $string));
        $this->assertTrue($container->has($key));
        $this->assertSame($string, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->has($key));
        $this->assertFalse($container->unbind($key));

        /* Test numbers */
        $this->assertTrue($container->bind($key, $number));
        $this->assertTrue($container->has($key));
        $this->assertSame($number, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->has($key));

        /* Test arrays */
        $this->assertTrue($container->bind($key, $array));
        $this->assertTrue($container->has($key));
        $this->assertSame($array, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->has($key));

        /* Test objects */
        $this->assertTrue($container->bind($key, $object));
        $this->assertTrue($container->has($key));
        $this->assertSame($object, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->has($key));

        /* Test classes */
        $this->assertTrue($container->bind($class));
        $this->assertTrue($container->has($class));
        $this->assertInstanceOf($class, $container->get($class));
        $this->assertTrue($container->unbind($class));
        $this->assertFalse($container->has($class));

        return $container;
    }

    /**
     * @depends testBinder
     */
    public function testMultipleBinder($container)
    {
        /* Variables */
        $key = 'key';
        $string = 'string';

        for ($x = 0; $x < 5; $x++) {
            $multiple[$key.$x] = $string.$x;
        }

        /* Test strings */
        $this->assertTrue($container->bindMultiple($multiple));
        $this->assertSame(array_values($multiple), $container->getMultiple(array_keys($multiple)));
        $this->assertTrue($container->unbindMultiple(array_keys($multiple)));
        $this->assertFalse($container->has($multiple[$key.'0']));
    }

    /**
     * @depends testBinder
     */
    public function testGetInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        /* Test strings */
        $container->get(1);
    }

    /**
     * @depends testBinder
     */
    public function testGetNotFoundException($container)
    {
        $this->expectException(NotFoundException::class);

        /* Test strings */
        $container->get('string');
    }

    /**
     * @depends testBinder
     */
    public function testHasInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        /* Test strings */
        $container->has(1);
    }

    /**
     * @depends testBinder
     */
    public function testBindInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        /* Test strings */
        $container->bind(1, 'value');
    }

    /**
     * @depends testBinder
     */
    public function testUnbindInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        /* Test strings */
        $container->unbind(1);
    }
}

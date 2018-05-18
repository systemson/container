<?php

namespace Amber\Container\Tests;

use Amber\Cache\Cache;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Injector;
use Amber\Container\Reflector;
use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testBinder()
    {
        $container = new Injector();

        /* Variables */
        $key = 'key';
        $string = 'string';
        $number = 1;
        $array = [1,2,3,4,5];
        $class = InjectableClass::class;
        $object = new $class();
        $reflector = new Reflector(ReceiverClass::class);

        /* Test strings */
        $this->assertTrue($container->bind($key, $string));
        $this->assertTrue($container->has($key));
        $this->assertSame($string, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->has($key));

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

        Cache::clear();

        return $container;
    }

    /**
     * @depends testBinder
     */
    public function testInjector($container)
    {
        $class = InjectableClass::class;

        /* Test classes */
        $this->assertTrue($container->bind($class));
        $this->assertTrue($container->has($class));
        $this->assertInstanceOf($class, $container->mount($class));
        $this->assertInstanceOf($class, $container->mount($class));
        $this->assertTrue($container->unbind($class));
        $this->assertFalse($container->has($class));

        Cache::clear();

        $this->expectException(InvalidArgumentException::class);

        $container->mount(UnknownClass::class);

    }
}

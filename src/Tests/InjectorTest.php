<?php

namespace Amber\Container\Tests;

use Amber\Container\Injector;
use Amber\Container\Reflector;
use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testBinder()
    {
        $container = new Injector();

        /* vars for binding */
        $key = 'value';
        $class = InjectableClass::class;
        $object = new $class();
        $reflector = new Reflector(ReceiverClass::class);

        /* Multiple binding */
        $this->assertTrue($container->bindMultiple([
            'key'    => $key,
            'class'  => $class,
            'object' => $object,
        ]));

        /* Unbind */
        $this->assertTrue($container->unbindMultiple(['object']));

        /* Test if unbinds worked */
        $this->assertFalse($container->unbind('object'));
        $this->assertFalse($container->has('object'));

        /* Test if the map has a "key" item */
        $this->assertTrue($container->has('key'));

        /* Test if the map key "key" returns value "value" */
        $this->assertSame('value', $container->get('key'));

        /* Bind string [object => Amber\Container\Tests\InjectableClass::class] */
        $this->assertTrue($container->bind('object', $class));

        /* Test if the map key object returns an instance of InjectableClass */
        $this->assertInstanceOf(InjectableClass::class, $container->get('object'));
    }

    public function testInjector()
    {
        $container = new Injector();
        $container->bind(InjectableClass::class);

        $this->assertInstanceOf(InjectableClass::class, $container->mount(InjectableClass::class));
    }
}

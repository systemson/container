<?php

namespace Amber\Container\Tests;

use Amber\Container\Injector;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /**
     * @expectedException Amber\Container\InvalidArgumentException
     */
    public function testContainer()
    {
        $container = new Injector();

        /* var for multiple binding */
        $key = 'value';
        $class = InjectableExampleClass::class;
        $object = new $class();

        /* Multiple binding */
        $this->assertTrue($container->bindMultiple([
            'key'    => $key,
            'class'  => $class,
            'object' => $object,
        ]));

        /* Unbind */
        $this->assertTrue($container->unbindMultiple(['object']));

        /* Check if unbinds worked */
        $this->assertFalse($container->unbind('object'));
        $this->assertFalse($container->has('object'));

        /* Checks if the map has a key item */
        $this->assertTrue($container->has('key'));

        /* Checks if the map key key returns value value */
        $this->assertSame('value', $container->get('key'));

        /* Bind string [object => Amber\Container\Tests\DIExampleClass::class] */
        $this->assertTrue($container->bind('object', $class));

        /* Checks if the map key object returns an instance of InjectableExampleClass */
        $this->assertSame(InjectableExampleClass::class, $container->get('object'));

        /* Checks if the Container returns an instance of DIExampleClass */
        $this->assertInstanceOf(
            DIExampleClass::class,
            $example = $container->getInstanceOf(DIExampleClass::class)
        );

        /* Checks for an invalid key type */
        $container->get(1);
    }
}

<?php

namespace Amber\Container\Tests;

use Amber\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testBinder()
    {
        $container = new Container();

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

        /* Checks if the map key attribute returns value value */
        $this->assertSame('value', $container->get('key'));

        /* Bind string [object => Amber\Container\Tests\DIExampleClass::class] */
        $this->assertTrue($container->bind('object', InjectableExampleClass::class));

        /* Checks if the map key attribute returns value value */
        $this->assertSame(InjectableExampleClass::class, $container->get('object'));

        /* */
        $this->assertInstanceOf(
            DIExampleClass::class,
            $example = $container->getInstanceOf(DIExampleClass::class)
        );
    }
}

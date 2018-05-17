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
        $this->assertSame(InjectableClass::class, $container->get('object'));

        /* Test if the Container returns an instance of ReceiverClass */
        /*$this->assertInstanceOf(
            ReceiverClass::class,
            $receiver = $container->getInstanceOf(ReceiverClass::class)
        );*/

        /* Test if returns null */
        $this->assertNull($container->getArguments());
        $this->assertNull($container->getArguments([]));
        $this->assertNull($container->getArguments([], []));

        /* Test if returns the arguments */
        $args = [
            'key'   => $key,
            'class' => $class,
        ];

        $this->assertEquals($args, $container->getArguments(
            $reflector->parameters,
            $args
        ));

        /* Test if returned arguments match the arguments needed for the instantiation */
        //$this->assertEquals([$key, $object], $container->getArguments($reflector->parameters));

        /* Test if the inject property was nulled */
        //$receiver->injected = null;
        //$this->assertNull($receiver->injected);

        /* Test if injection returns the same object */
        //$this->assertSame($receiver, $container->inject($receiver, $reflector->injectables));

        /* Test if injection works */
        //$this->assertInstanceOf($class, $receiver->injected);
    }
}

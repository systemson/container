<?php

namespace Amber\Container\Tests;

use Amber\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testMapBind()
    {
        /* var for multiple binding */
        $key = 'value';
        $class = InjectableExampleClass::class;
        $object = new $class();

        /* Multiple binding */
        $this->assertTrue(Container::bindMultiple([
            'key' => $key,
            'class' => $class,
            'object' => $object,
        ]));

        /* Unbinds */
        $this->assertTrue(Container::unbind('key'));
        $this->assertTrue(Container::unbindMultiple(['object']));

        /* Check if unbinds worked */
        $this->assertFalse(Container::unbind('object'));

        /* Bind string [key => value] */
        $this->assertTrue(Container::bind('key', $key));

        /* Checks if map property has a key attribute */
        $this->assertObjectHasAttribute('key', Container::$map);

        /* Checks if the map key attribute returns value value */
        $this->assertSame('value', Container::get('key'));


        /* Bind string [object => Amber\Container\Tests\DIExampleClass::class] */
        $this->assertTrue(Container::bind('object', InjectableExampleClass::class));

        /* Checks if map property has a key attribute */
        $this->assertObjectHasAttribute('object', Container::$map);

        /* Checks if the map key attribute returns value value */
        $this->assertSame(InjectableExampleClass::class, Container::get('object'));

        /* */
        $this->assertInstanceOf(
            DIExampleClass::class,
            $example = Container::getInstanceOf(DIExampleClass::class)
        );

        $params =  [
            (object) ['name' => 'key'],
            (object) ['name' => 'class'],
        ];

        $this->assertEquals(['value', $object], Container::getParametersFromMap($params));

        //$this->assertEquals($params, $params);
    }

    /**
     * @expectedException Amber\Container\ContainerException
     */
    public function testContainerException()
    {
        Container::getInstanceOf(NoneExistentClass::class);
    }
}

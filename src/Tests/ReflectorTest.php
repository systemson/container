<?php

namespace Amber\Container\Tests;

use Amber\Container\Reflector;
use Amber\Container\Tests\Example\Controller;
use Amber\Container\Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase
{
    public function testReflector()
    {
        $class = Controller::class;
        $reflection = new Reflector($class);

        /* Test reflection instance. */
        $this->assertInstanceOf(
            \ReflectionClass::class,
            $reflection->reflection
        );

        /* Test if the instance returned by inflector is an instance of ReflectorClass. */
        $this->assertInstanceOf(
            $class,
            $reflection->newInstance([1, new Model()])
        );

        /* Test if the Reflector class reads the injectable properties */
        $this->assertSame(
            'view',
            $reflection->getInjectables()[0]->name
        );

        /* Test that the injectable property prevents from being readed twice. */
        $this->assertSame(
            $reflection->getInjectables(),
            $reflection->getInjectables()
        );
    }
}

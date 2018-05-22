<?php

namespace Amber\Container\Tests;

use Amber\Container\Reflector;
use Amber\Container\Tests\Example\InjectableClass;
use Amber\Container\Tests\Example\ReflectorClass;
use Amber\Container\Tests\Example\ReflectorWithParamsClass;
use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase
{
    public function testReflector()
    {
        $class = ReflectorClass::class;
        $reflection = new Reflector($class);

        /* Test reflection instance. */
        $this->assertInstanceOf(
            \ReflectionClass::class,
            $reflection->reflection
        );

        /* Test if the instance returned by inflector is an instance of ReflectorClass. */
        $this->assertInstanceOf(
            $class,
            $reflection->newInstance()
        );

        /* Test if the ReflectorClass reads the injectable properties */
        $this->assertSame(
            'inject',
            $reflection->getInjectables()[0]->name
        );

        /* Test that the injectable property prevents from being readed twice. */
        $this->assertSame(
            $reflection->getInjectables(),
            $reflection->getInjectables()
        );
    }

    public function testReflectorWithParams()
    {
        $class = ReflectorWithParamsClass::class;
        $reflection = new Reflector($class);

        /* Test new instance with arguments for the constructor */
        $this->assertInstanceOf(
            $class,
            $reflection->newInstance([new InjectableClass()])
        );
    }
}

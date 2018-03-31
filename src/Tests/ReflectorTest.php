<?php

namespace Amber\Container\Tests;

use Amber\Container\Reflector;
use PHPUnit\Framework\TestCase;

class ReflectorTest extends TestCase
{
    public function testReflector()
    {
        $class = ReflectorExampleClass::class;
        $reflection = new Reflector($class);

        $this->assertInstanceOf(
            \ReflectionClass::class,
            $reflection->reflection
        );

        $this->assertInstanceOf(
            $class,
            $reflection->newInstance()
        );

        $this->assertSame(
            'inject',
            $reflection->getInjectableProperties()[0]->name
        );
    }
}

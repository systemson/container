<?php

namespace Amber\Container\Tests;

use Amber\Cache\Cache;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Injector;
use Amber\Container\Tests\Example\Controller;
use Amber\Container\Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testInjector()
    {
        $container = new Injector();

        $model = Model::class;

        /* Test classes */
        $this->assertTrue($container->bind($model));
        $this->assertTrue($container->has($model));
        $this->assertInstanceOf($model, $container->get($model));
        $this->assertInstanceOf($model, $container->mount($model));
        $this->assertInstanceOf($model, $container->mount($model));

        $controller = Controller::class;

        /* Test classes */
        $this->assertTrue($container->bind('id', 5));
        $this->assertTrue($container->bind($controller));
        $this->assertTrue($container->has($controller));

        $instance = $container->get($controller);
        $this->assertInstanceOf($controller, $instance);
        $this->assertInstanceOf($model, $instance->getModel());
        $this->assertTrue($container->unbind($model));
        $this->assertTrue($container->unbind($controller));

        $this->assertFalse($container->has($model));
        $this->assertFalse($container->has($controller));

        Cache::clear();

        return $container;
    }

    /**
     * @depends testInjector
     */
    public function testException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        $container->mount(UnknownClass::class);
    }
}

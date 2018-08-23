<?php

namespace Tests;

use Amber\Cache\Cache;
use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Injector;
use Tests\Example\Controller;
use Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testInjector()
    {
        $container = new Injector();

        $model = Model::class;
        $controller = Controller::class;

        /* Bind dependencies */
        $this->assertTrue($container->bind('id', 1));
        $this->assertTrue($container->bind($model));

        //$this->assertTrue($container->has($model));
        $this->assertInstanceOf(
            $controller,
            $container->mount(
                $controller,
                [
                    1,
                    $container->get($model),
                ]
            )
        );
        $this->assertInstanceOf($controller, $container->mount($controller));

        $controller = Controller::class;

        /* Test classes */
        $this->assertFalse($container->bind('id', 5));
        $this->assertFalse($container->bind($controller));

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
     *
     * @depends testInjector
     */
    public function testInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        $container->mount(UnknownClass::class);
    }

    /**
     *
     * @depends testInjector
     */
    public function testNotFoundException($container)
    {
        $this->expectException(NotFoundException::class);

        $container->mount(Controller::class);
    }
}
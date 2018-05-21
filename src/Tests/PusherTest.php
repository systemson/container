<?php

namespace Amber\Container\Tests;

use Amber\Container\Injector;
use Amber\Container\Tests\Example\Controller;
use Amber\Container\Tests\Example\Model;
use Amber\Container\Tests\Example\View;
use PHPUnit\Framework\TestCase;

class PusherTest extends TestCase
{
    public function testPush()
    {
        $container = new Injector();

        $view = view::class;
        $model = Model::class;
        $controller = Controller::class;

        /* Bind dependecies */
        $this->assertTrue($container->bind('id', 1));
        $this->assertTrue($container->bind($view));
        $this->assertTrue($container->bind($model));
        $this->assertTrue($container->bind($controller));

        $instance = $container->get($controller);
        $instance = $container->push($instance, ['view' => $view]);

        $this->assertInstanceOf($controller, $instance);
        $this->assertInstanceOf($view, $instance->getView());
        $this->assertInstanceOf($model, $instance->getModel());

        return $container;
    }

    /**
     * @depends testPush
     */
    public function testInject($container)
    {
        $view = view::class;
        $model = Model::class;
        $controller = Controller::class;

        /* Bind dependecies */
        $this->assertTrue($container->has('id'));
        $this->assertTrue($container->has($view));
        $this->assertTrue($container->has($model));
        $this->assertTrue($container->has($controller));

        $instance = $container->get($controller);
        $instance = $container->inject($instance);

        $this->assertInstanceOf($controller, $instance);
        $this->assertInstanceOf($view, $instance->getView());

        return $container;
    }
}

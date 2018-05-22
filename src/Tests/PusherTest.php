<?php

namespace Amber\Container\Tests;

use Amber\Container\Injector;
use Amber\Container\Tests\Example\Controller;
use Amber\Container\Tests\Example\Model;
use Amber\Container\Tests\Example\View;
use PHPUnit\Framework\TestCase;

class PusherTest extends TestCase
{
    public function testPusher()
    {
        $container = new Injector();

        $view = view::class;
        $model = Model::class;
        $controller = Controller::class;

        /* Binds dependecies */
        $this->assertTrue($container->bind('id', 1));
        $this->assertTrue($container->bind($view));
        $this->assertTrue($container->bind($model));
        $this->assertTrue($container->bind($controller));

        /* Instantiate the controller */
        $instance = $container->get($controller);

        /* Push the view class to the view property */
        $instance = $container->push($instance, ['view' => $view]);

        /* Validations */
        $this->assertInstanceOf($controller, $instance);
        $this->assertInstanceOf($view, $instance->getView());
        $this->assertInstanceOf($model, $instance->getModel());

        return $container;
    }

    /**
     * @depends testPusher
     */
    public function testInjection($container)
    {
        $view = view::class;
        $model = Model::class;
        $controller = Controller::class;

        /* Instantiate the controller */
        $instance = $container->get($controller);

        /* Inject the view instance */
        $instance = $container->inject($instance);

        /* Validations */
        $this->assertInstanceOf($controller, $instance);
        $this->assertInstanceOf($view, $instance->getView());
        $this->assertInstanceOf($model, $instance->getModel());

        return $container;
    }
}

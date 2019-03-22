<?php

namespace Tests;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Container;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\View;
use PHPUnit\Framework\TestCase;

class BinderTest extends TestCase
{
    public function testContainer()
    {
        $container = new Container();

        $this->assertTrue($container->bind(Controller::class));
        $this->assertTrue($container->bind(Model::class));
        $this->assertTrue($container->bind(View::class));

        $this->assertFalse($container->bind(Controller::class));
        $this->assertFalse($container->bind(Model::class));
        $this->assertFalse($container->bind(View::class));

        $controller = $container->get(Controller::class);
        $this->assertInstanceOf(Controller::class, $controller);

        $model = $controller->getModel();
        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals(1, $model->getId());

        $view = $controller->getView();
        $this->assertInstanceOf(View::class, $view);

        return $container;
    }

    /**
     * @depends testContainer
     */
    public function testSingleton($container)
    {
        $service = $container->locate(Controller::class);

        $service->singleton();

        $this->assertInstanceOf(Controller::class, $container->get(Controller::class));
        $this->assertInstanceOf(Controller::class, $container->get(Controller::class));
    }

    /**
     * @depends testContainer
     */
    public function testSetInstance($container)
    {
        $service = $container->locate(Model::class);

        $model = $container->get(Model::class);

        $service->setInstance($model);

        $this->assertInstanceOf(Model::class, $container->get(Model::class));
        $this->assertInstanceOf(Model::class, $container->get(Model::class));
        $this->assertSame($model, $container->get(Model::class));

        $service->clear();
        $this->assertNotSame($model, $container->get(Model::class));
    }

    /**
     * @depends testContainer
     */
    public function testGetInvalidArgumentException($container)
    {
        $this->expectException(InvalidArgumentException::class);

        $service = $container->locate(View::class);

        $service->setInstance(new Model());
    }
}

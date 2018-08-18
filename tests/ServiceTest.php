<?php

namespace Tests;

use Amber\Container\Exception\ContainerException;
use Amber\Container\Injector;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\View;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testService()
    {
        $container = new Injector();

        $model = Model::class;
        $view = View::class;
        $controller = Controller::class;
        $arguments = [
            1,
            new Model(),
        ];

        /* Test classes */
        $this->assertTrue($container->bind('id', 1));
        $this->assertTrue($container->bind($model));
        $this->assertTrue($container->bind($view));
        $this->assertTrue($container->bind($controller));

        $service = $container->locate($controller);

        $this->assertSame($controller, $service->key);
        $this->assertSame($controller, $service->value);

        /* Test getParameters() */
        $this->assertSame('id', $service->getParameters()[0]->name);
        $this->assertSame('model', $service->getParameters()[1]->name);
        $this->assertSame($model, $service->getParameters()[1]->getClass()->name);

        /* Test getInjectables() */
        $this->assertSame('view', $service->getInjectables()[0]->name);
        $this->assertSame($view, $service->getInjectables()[0]->inject);

        /* Test getInstance() */
        $instance = $container->get($controller);
        $service->singleton(true);
        $this->assertEquals($instance, $service->getInstance($arguments));
        $this->assertTrue($service->isSingleton());
        $this->assertInstanceOf($controller, $service->getInstance($arguments));

        /* Test setInstance */
        $service->clear();
        $this->assertFalse($service->isSingleton());
        $this->assertEquals($instance, $service->getInstance($arguments));
        $this->assertInstanceOf($controller, $service->getInstance($arguments));

        /* Test setArguments() */
        $service->setArguments($arguments);
        $this->assertSame($arguments, $service->getArguments());

        return $container->locate('id');
    }

    /**
     *
     * @depends testService
     */
    public function testGetParametersException($service)
    {
        $this->expectException(ContainerException::class);

        $service->getParameters();
    }

    /**
     *
     * @depends testService
     */
    public function testGetInjectablesException($service)
    {
        $this->expectException(ContainerException::class);

        $service->getInjectables();
    }

    /**
     *
     * @depends testService
     */
    public function testGetInstanceException($service)
    {
        $this->expectException(ContainerException::class);

        $service->getInstance();
    }

    /**
     *
     * @depends testService
     */
    public function testSingletonException($service)
    {
        $this->expectException(ContainerException::class);

        $service->singleton(true);
    }

    /**
     *
     * @depends testService
     */
    public function testSetArgumentsException($service)
    {
        $this->expectException(ContainerException::class);

        $service->setArguments([]);
    }
}

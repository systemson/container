<?php

namespace Tests;

use Amber\Container\Service\ServiceClass;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\View;
use PHPUnit\Framework\TestCase;
use BadMethodCallException;

class ServiceTest extends TestCase
{
    public function testBasic()
    {
        $service = new ServiceClass(Controller::class);
        $reflection = new \ReflectionClass(Controller::class);

        $this->assertInstanceOf(\ReflectionClass::class, $service->getReflection());
        $this->assertEquals($reflection, $service->getReflection());

        $model = new Model();
        $view = new View();

        $controller = new Controller($model, $view);

        $this->assertInstanceOf(ServiceClass::class, $service->setInstance($controller));

        $this->assertSame($controller, $service->getInstance());
        $this->assertTrue($service->isSingleton());
        $this->assertSame($controller, $service->getInstance());

        $this->assertInstanceOf(ServiceClass::class, $service->setInstance(function () use ($controller) {
            return $controller;
        }));

        $this->assertSame($controller, $service->getInstance());
        $this->assertTrue($service->isSingleton());
        $this->assertSame($controller, $service->getInstance());

        $this->assertInstanceOf(ServiceClass::class, $service->clear());

        $this->assertEquals($controller, $service->getInstance([$model, $view]));
        $this->assertFalse($service->isSingleton());

        $this->assertEquals($controller, $service->getInstance([$model, $view]));

        $this->assertEquals(
            $reflection->getMethod('__construct')->getParameters(),
            $service->getParameters()
        );

        $this->assertEquals(
            $reflection->getMethod('setId')->getParameters(),
            $service->getParameters('setId')
        );

        $this->assertInstanceOf(ServiceClass::class, $service->setArgument(Model::class, $model, '__construct'));
        $this->assertInstanceOf(ServiceClass::class, $service->setArgument(View::class, $view, '__construct'));

        $this->assertEquals($model, $service->getArgument(Model::class, '__construct'));
        $this->assertEquals($view, $service->getArgument(View::class, '__construct'));
        $this->assertEquals(
            [
                Model::class => $model,
                View::class => $view,
            ],
            $service->getArguments('__construct')
        );

        $this->assertInstanceOf(ServiceClass::class, $service->setArguments(
            [
                Model::class => $model,
                View::class => $view,
            ],
            '__construct',
        ));

        $this->assertEquals($model, $service->getArgument(Model::class, '__construct'));
        $this->assertEquals($view, $service->getArgument(View::class, '__construct'));
        $this->assertEquals(
            [
                Model::class => $model,
                View::class => $view,
            ],
            $service->getArguments('__construct')
        );
    }

    public function testOthers()
    {
        $int = 5;

        $model = new Model();
        $view = new View();

        $service = (new ServiceClass(Controller::class))->afterConstruct('setId', $int);
        $controller = $service->getInstance([$model, $view]);

        $this->assertEquals($int, $controller->getId());
    }

    public function testException()
    {
        $this->expectException(BadMethodCallException::class);

        (new ServiceClass(View::class))->afterConstruct('setId');
    }

    public function testProperties()
    {
        $service = (new ServiceClass(Controller::class));
        $property = $service->getProperty('model');

        if ($property->hasType()) {
            $this->assertEquals(Model::class, $property->getType());
        }

        if (!$property->hasValue()) {
            $property->setValue(Model::class);

            $this->assertEquals(Model::class, $property->getValue());
        }

        $this->assertEquals($property, $service->getProperty('model'));

        $service->injectProperty('model');

        $type = current($service->getInjectables())->getType();

        $this->assertEquals(Model::class, $type);
    }

    public function testServiceGlobalArguments()
    {
        $service = (new ServiceClass(Controller::class));

        $model = new Model();
        $view = new View();

        $service
            ->setArgument(Model::class, $model)
            ->setArgument(View::class)
        ;

        $this->assertEquals($model, $service->getArgument(Model::class, '__construct'));
        $this->assertInstanceOf(ServiceClass::class, $service->getArgument(View::class));
    }
}

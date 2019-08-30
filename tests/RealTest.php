<?php

namespace Tests;

use Amber\Container\Container;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\ChildModel;
use Tests\Example\View;
use PHPUnit\Framework\TestCase;
use Amber\Container\Exception\NotFoundException;

/**
 * @todo MUST test depencies that need other depdencies recursively.
 */
class RealTest extends TestCase
{
    public function testSingleton()
    {
        $container = new Container();

        $container->bind(Model::class);
        $container->bind(View::class);

        $service = $container->singleton(Controller::class)
        ->setArguments(['optional' => 2])
        ->afterConstruct('setId', 53);

        $controller = $container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);
        $this->assertSame($controller, $container->get(Controller::class));
        $this->assertEquals(53, $controller->id);
    }

    public function testSingletonWithAlias()
    {
        $container = new Container();

        $service = $container->singleton(Model::class, ChildModel::class);

        $model = $container->get(Model::class);

        $this->assertInstanceOf(Model::class, $model);
        $this->assertSame($model, $container->get(Model::class));

        // Must throw NotFoundException since the ChildModel is not binded by it's own name but by it's father name
        try {
            $container->get(ChildModel::class);
        } catch (\Exception $e) {
            $this->assertInstanceOf(NotFoundException::class, $e);
        }
    }

    public function testClosure()
    {
        $container = new Container();

        $container->bind(Model::class);
        $container->bind(View::class);
        
        $callback = $container->getClosureFor(Controller::class, 'index', ['name' => 'world']);

        $this->assertEquals('Hello world.', $callback());
    }

    public function testClosureForBoolean()
    {
        $container = new Container();

        $container->bind(Model::class);
        $container->bind(View::class);
        
        $callback = $container->getClosureFor(Controller::class, 'setBoolean', ['boolean' => true]);

        $this->assertEquals(true, $callback());
    }

    public function testBindingAllDirectlyToService()
    {
        $container = new Container();

        $service = $container->register(Controller::class)
        ->setArgument(View::class)
        ->setArgument(Model::class)
        ->setArgument('optional', 2);

        $controller = $container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);
        $this->assertEquals($controller, $container->get(Controller::class));
        $this->assertNotSame($controller, $container->get(Controller::class));
    }

    public function testBindingClosureDirectlyToService()
    {
        $container = new Container();

        $service = $container->register(Controller::class)
        ->setArgument(View::class, function () {
            return new View();
        })
        ->setArgument(Model::class)
        ->setArgument('optional', 2)
        ->afterConstruct('setId', function () {
            return 53;
        });

        $controller = $container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);
        $this->assertEquals($controller, $container->get(Controller::class));
        $this->assertNotSame($controller, $container->get(Controller::class));
        $this->assertEquals(53, $controller->id);
    }
}

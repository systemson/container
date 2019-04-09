<?php

namespace Tests;

use Amber\Container\Container;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\View;
use PHPUnit\Framework\TestCase;

/**
 * @todo MUST test depencies that need other depdencies recursively.
 */
class RealTest extends TestCase
{
    public function testRegister()
    {
        $container = new Container();

        $container->bind(Model::class);
        $container->bind(View::class);

        $service = $container->register(Controller::class)
        ->singleton()
        ->setArguments(['optional' => 2])
        ->afterConstruct('setId', 53);

        $controller = $container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);
        $this->assertSame($controller, $container->get(Controller::class));
        $this->assertEquals(53, $controller->id);
    }

    public function testClosure()
    {
        $container = new Container();

        $container->bind(Model::class);
        $container->bind(View::class);
        
        $callback = $container->getClosureFor(Controller::class, 'index', ['name' => 'world']);

        $this->assertEquals('Hello world.', $callback());
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
        ->afterConstruct('setId', function() {
            return 53;
        });

        $controller = $container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);
        $this->assertEquals($controller, $container->get(Controller::class));
        $this->assertNotSame($controller, $container->get(Controller::class));
        $this->assertEquals(53, $controller->id);
    }
}

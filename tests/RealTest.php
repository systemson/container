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
        ->bindArguments(['optional' => 2], '__construct')
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

        $callback = $container->getClosureFor(Controller::class, 'setBoolean', ['value' => true]);

        $this->assertTrue($callback->__invoke());
    }

    public function testBindingAllDirectlyToService()
    {
        $container = new Container();

        $service = $container->register(Controller::class)
            ->bindArgument(View::class, null, '__construct')
            ->bindArgument(Model::class, null, '__construct')
            ->bindArgument('optional', 2, '__construct')
        ;

        $controller = $container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);
        $this->assertEquals($controller, $container->get(Controller::class));
        $this->assertNotSame($controller, $container->get(Controller::class));
    }

    public function testBindingClosureDirectlyToService()
    {
        $container = new Container();

        $service = $container->register(Controller::class)
        ->bindArgument(View::class, function () {
            return new View();
        }, '__construct')
        ->bindArgument(Model::class, null, '__construct')
        ->bindArgument('optional', 2, '__construct')
        ->afterConstruct('setId', function () {
            return 53;
        });

        $controller = $container->get(Controller::class);

        $this->assertInstanceOf(Controller::class, $controller);
        $this->assertEquals($controller, $container->get(Controller::class));
        $this->assertNotSame($controller, $container->get(Controller::class));
        $this->assertEquals(53, $controller->id);
    }

    public function testArgumentTypes()
    {
        $container = new Container();

        $container->bind(View::class);
        $container->bind(Model::class);

        /* Test strings */
        $boolean = $container->getClosureFor(Controller::class, 'setBoolean', ['value' => '1']);
        $int = $container->getClosureFor(Controller::class, 'setInt', ['value' => '12145']);

        $this->assertEquals(true, $boolean->__invoke());
        $this->assertEquals('12145', $int->__invoke());
    }

    public function testParameterInjection()
    {
        $container = new Container();

        $container->bind(View::class);
        $container->bind(Model::class);

        /* Test strings */
        $service = $container->register(Controller::class)
            ->injectProperty('model')
            ->injectProperty('view')
        ;

        $controller = $container->get(Controller::class);

        $this->assertEquals($container->get(View::class), $controller->view);
        $this->assertEquals($container->get(Model::class), $controller->model);
    }
}

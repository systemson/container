<?php

namespace Tests;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Container;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\View;
use PHPUnit\Framework\TestCase;

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


    	$reflection = new \ReflectionClass(Controller::class);
    	$constructor = $reflection->getConstructor();
    	$params = $constructor->getParameters();

    	$controller = $container->get(Controller::class);

    	$this->assertInstanceOf(Controller::class, $controller);
        $this->assertSame($controller, $container->get(Controller::class));
        $this->assertEquals(53, $controller->id);
    }
}
<?php

namespace Tests;

use Amber\Container\Invoker;
use Tests\Example\Controller;
use Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class InvokerTest extends TestCase
{
    public function testInvokerWithConstructorParams()
    {
        $invoker = new Invoker();

        $id = ['id' => 1];
        $class = [Model::class => Model::class];

        $model = $invoker->from(Controller::class)
        ->buildWith($id, $class)
        ->call('getModel')
        ->do();

        $this->assertInstanceOf(Model::class, $model);
    }

    public function testInvokerWithoutConstructorParams()
    {
        $invoker = new Invoker();

        $id = $invoker->from(Model::class)
        ->call('getId')
        ->do();

        $this->assertSame(1, $id);
    }

    public function testStaticInvoker()
    {
        $args = [
            1,
            new Model(),
        ];

        $closure = Invoker::getClosure('Tests\Example\Controller@getModel', $args);

        $this->assertInstanceOf(Model::class, $closure());
    }
}

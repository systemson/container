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

    public function testInvokerBeforeAction()
    {
        $invoker = new Invoker();

        $new_id = 2;

        $result = $invoker->from(Model::class)
        ->call('getId')
        ->before('setId', $new_id) // Changes the return id to $new_id's value.
        ->after('setId', $new_id + 1) // Changes the return id to $new_id's value plus 1. But won't afect the $result value.
        ->do();

        $this->assertSame($new_id, $result);
    }

    public function testInvokerAfterAction()
    {
        $invoker = new Invoker();

        $new_id = 2;

        $closure = Invoker::getClosure('\Tests\Example\Model::getId');
        $closure->setAfterAction('setId', [$new_id]);

        $this->assertSame(1, $closure());

        $this->assertSame($new_id, $closure());
    }

    public function testStaticInvoker()
    {
        $args = [
            1,
            new Model(),
        ];

        $closure = Invoker::getClosure('\Tests\Example\Controller::getModel', $args);

        $this->assertInstanceOf(Model::class, $closure());
    }
}

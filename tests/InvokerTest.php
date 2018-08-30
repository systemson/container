<?php

namespace Tests;

use Amber\Container\Invoker;
use Tests\Example\Controller;
use Tests\Example\Model;
use PHPUnit\Framework\TestCase;

class InvokerTest extends TestCase
{
    public function testInvoker()
    {
        $invoker = new Invoker();

        $args = [
            'id' => 1,
            Model::class => Model::class,
        ];

        $model = $invoker->from(Controller::class)
        ->with($args)
        ->call('getModel')
        ->do();

        $this->assertInstanceOf(Model::class, $model);
    }

    public function testStaticInvoker()
    {
        $args = [
            1,
            new Model(),
        ];

        $closure = Invoker::getClosure('Tests\Example\Controller', 'getModel', $args);

        $this->assertInstanceOf(Model::class, $closure());
    }
}

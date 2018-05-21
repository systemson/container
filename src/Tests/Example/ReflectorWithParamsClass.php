<?php

namespace Amber\Container\Tests\Example;

class ReflectorWithParamsClass
{
    public $object;

    public function __construct(InjectableClass $object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }
}

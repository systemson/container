<?php

namespace Amber\Container\Tests;

class DIExampleClass
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var object Amber\Container\Tests\InjectableExampleClass::class
     */
    public $object;

    public function __construct(string $key, InjectableExampleClass $object)
    {
        $this->key = $key;

        $this->object = $object;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getObject()
    {
        return $this->object;
    }
}

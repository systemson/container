<?php

namespace Amber\Container\Tests;

class ReceiverClass
{
    /**
     * @inject Amber\Container\Tests\InjectableClass
     *
     * @var string
     */
    public $injected;

    /**
     * @var string
     */
    public $key;

    /**
     * @var object Amber\Container\Tests\InjectableClass::class
     */
    public $object;

    public function __construct(string $key, InjectableClass $object)
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

<?php

namespace Tests;

use Amber\Container\Container;
use Amber\Cache\Driver\SimpleCache as Cache;
use Tests\Example\Model;
use Tests\Example\Controller;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testContainer()
    {
        $container = new Container();

        $container->setCache(new Cache(getcwd() . '/tmp/cache/services'));

        /* Variables */
        $key = 'key';
        $string = 'string';
        $number = 1;
        $array = [1, 2, 3, 4, 5];
        $class = Model::class;
        $object = new $class();

        $container->bindMultiple([
        	$key => $string,
        	'number' => $number,
        	'array' => $array,
        	'class' => $class,
        	'object' => $object
        ]);

        $container->drop();

        $container->clear();

        $this->assertFalse($container->has($key));
        $this->assertFalse($container->has('number'));
        $this->assertFalse($container->has('array'));
        $this->assertFalse($container->has('class'));
        $this->assertFalse($container->has('object'));

        $container->pick();

        $this->assertTrue($container->has($key));
        $this->assertTrue($container->has('number'));
        $this->assertTrue($container->has('array'));
        $this->assertTrue($container->has('class'));
        $this->assertTrue($container->has('object'));

        $container->drop();
        $container->pick();

        $this->assertTrue($container->has($key));
        $this->assertTrue($container->has('number'));
        $this->assertTrue($container->has('array'));
        $this->assertTrue($container->has('class'));
        $this->assertTrue($container->has('object'));

        $container->clear();
        $container->getCache()->clear();

        return $container;
    }
}

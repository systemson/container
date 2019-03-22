<?php

namespace Tests;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\App;
use Tests\Example\Controller;
use Tests\Example\Model;
use Tests\Example\View;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testApp()
    {
        /* Variables */
        $key = 'key';
        $string = 'string';
        $number = 1;
        $array = [1, 2, 3, 4, 5];
        $class = Model::class;
        $anonymous  = new class extends Model {
        };
        $object = new $class();
        $function = function ($value) {
            return $value;
        };

        /* Tests strings */
        $this->assertTrue(App::bind($key, $string));
        $this->assertFalse(App::bind($key, $string));
        $this->assertTrue(App::has($key));
        $this->assertSame($string, App::get($key));
        $this->assertTrue(App::unbind($key));
        $this->assertFalse(App::has($key));
        $this->assertFalse(App::has($key));
        $this->assertFalse(App::unbind($key));

        /* Tests numbers */
        $this->assertTrue(App::bind($key, $number));
        $this->assertFalse(App::bind($key, $number));
        $this->assertTrue(App::has($key));
        $this->assertSame($number, App::get($key));
        $this->assertTrue(App::unbind($key));
        $this->assertFalse(App::unbind($key));
        $this->assertFalse(App::has($key));

        /* Tests arrays */
        $this->assertTrue(App::bind($key, $array));
        $this->assertFalse(App::bind($key, $array));
        $this->assertTrue(App::has($key));
        $this->assertSame($array, App::get($key));
        $this->assertTrue(App::unbind($key));
        $this->assertFalse(App::unbind($key));
        $this->assertFalse(App::has($key));

        /* Tests objects */
        $this->assertTrue(App::bind($key, $object));
        $this->assertFalse(App::bind($key, $object));
        $this->assertTrue(App::has($key));
        $this->assertSame($object, App::get($key));
        $this->assertTrue(App::unbind($key));
        $this->assertFalse(App::unbind($key));
        $this->assertFalse(App::has($key));

        /* Tests classes */
        $this->assertTrue(App::bind($class));
        $this->assertFalse(App::bind($class));
        $this->assertTrue(App::has($class));
        $this->assertInstanceOf($class, App::get($class));
        $this->assertTrue(App::unbind($class));
        $this->assertFalse(App::unbind($class));
        $this->assertFalse(App::has($class));

        /* Tests anonymous classes */
        $this->assertTrue(App::bind($key, $anonymous));
        $this->assertFalse(App::bind($key, $anonymous));
        $this->assertTrue(App::has($key));
        $this->assertSame($anonymous, App::get($key));
        $this->assertInstanceOf(Model::class, App::get($key));
        $this->assertTrue(App::unbind($key));
        $this->assertFalse(App::unbind($key));
        $this->assertFalse(App::has($key));

        /* Tests anonymous function */
        $this->assertTrue(App::bind($key, $function));
        $this->assertFalse(App::bind($key, $function));
        $this->assertTrue(App::has($key));
        $this->assertSame($function, App::get($key));
        $this->assertTrue(App::unbind($key));
        $this->assertFalse(App::unbind($key));
        $this->assertFalse(App::has($key));

        App::clear();
    }

    public function testMake()
    {
        $this->assertTrue(App::bind(Model::class));
        $this->assertTrue(App::bind(View::class));
        
        $this->assertInstanceOf(Controller::class, App::make(Controller::class));
    }
}

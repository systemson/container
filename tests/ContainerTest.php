<?php

namespace Tests;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Container\Exception\NotFoundException;
use Amber\Container\Container;
use Tests\Example\Model;
use Tests\Example\View;
use Tests\Example\Controller;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testContainer()
    {
        $container = new Container();

        /* Variables */
        $key = 'key';
        $string = 'string';
        $number = 1;
        $array = [1, 2, 3, 4, 5];
        $class = Model::class;
        $anonymous  = new class extends Model {
        };
        $object = new $class();
        $function = function () use ($string) {
            return $string;
        };

        /* Tests strings */
        $this->assertTrue($container->bind($key, $string));
        $this->assertFalse($container->bind($key, $string));
        $this->assertTrue($container->has($key));
        $this->assertSame($string, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->has($key));
        $this->assertFalse($container->has($key));
        $this->assertFalse($container->unbind($key));

        /* Tests numbers */
        $this->assertTrue($container->bind($key, $number));
        $this->assertFalse($container->bind($key, $number));
        $this->assertTrue($container->has($key));
        $this->assertSame($number, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->unbind($key));
        $this->assertFalse($container->has($key));

        /* Tests arrays */
        $this->assertTrue($container->bind($key, $array));
        $this->assertFalse($container->bind($key, $array));
        $this->assertTrue($container->has($key));
        $this->assertSame($array, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->unbind($key));
        $this->assertFalse($container->has($key));

        /* Tests objects */
        $this->assertTrue($container->bind($key, $object));
        $this->assertFalse($container->bind($key, $object));
        $this->assertTrue($container->has($key));
        $this->assertSame($object, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->unbind($key));
        $this->assertFalse($container->has($key));

        /* Tests classes */
        $this->assertTrue($container->bind($class));
        $this->assertFalse($container->bind($class));
        $this->assertTrue($container->has($class));
        $this->assertInstanceOf($class, $container->get($class));
        $this->assertTrue($container->unbind($class));
        $this->assertFalse($container->unbind($class));
        $this->assertFalse($container->has($class));

        /* Tests anonymous classes */
        $this->assertTrue($container->bind($key, $anonymous));
        $this->assertFalse($container->bind($key, $anonymous));
        $this->assertTrue($container->has($key));
        $this->assertSame($anonymous, $container->get($key));
        $this->assertInstanceOf(Model::class, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->unbind($key));
        $this->assertFalse($container->has($key));

        /* Tests anonymous function */
        $this->assertTrue($container->bind($key, $function));
        $this->assertFalse($container->bind($key, $function));
        $this->assertTrue($container->has($key));
        $this->assertSame($function(), $container->get($key));
        $this->assertSame($string, $container->get($key));
        $this->assertTrue($container->unbind($key));
        $this->assertFalse($container->unbind($key));
        $this->assertFalse($container->has($key));

        $container->clear();

        return $container;
    }

    /**
     * @depends testContainer
     */
    public function testMultipleBinder($container)
    {
        /* Variables */
        $key = 'key';
        $string = 'string';

        for ($x = 0; $x < 5; $x++) {
            $multiple[$key . $x] = $string . $x;
        }

        /* Test strings */
        $this->assertTrue($container->bindMultiple($multiple));
        $this->assertSame(array_values($multiple), $container->getMultiple(array_keys($multiple)));
        $this->assertTrue($container->unbindMultiple(array_keys($multiple)));
        $this->assertFalse($container->has($multiple[$key . '0']));

        $container->clear();
    }

    /**
     * @depends testContainer
     */
    public function testGetInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->get(1);
    }

    /**
     * @depends testContainer
     */
    public function testGetNotFoundException($container)
    {
        $container->clear();
        $this->expectException(NotFoundException::class);

        $container->get('string');
    }

    /**
     * @depends testContainer
     */
    public function testHasInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->has(1);
    }

    /**
     * @depends testContainer
     */
    public function testBindInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->bind(1, 'value');
    }

    /**
     * @depends testContainer
     */
    public function testUnbindInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->unbind(1);
    }

    /**
     * @depends testContainer
     */
    public function testLocateInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->locate(1);
    }

    /**
     * @depends testContainer
     */
    public function testPutInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->put(1);
    }

    /**
     * @depends testContainer
     */
    public function testPutInvalidArgumentException2($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->put('this_is_not_a_class');
    }

    /**
     * @depends testContainer
     */
    public function testPutInvalidArgumentException3($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->put(Controller::class, 'this_is_not_a_class');
    }

    /**
     * @depends testContainer
     */
    public function testMakeInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->make(1);
    }

    /**
     * @depends testContainer
     */
    public function testRegisterInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        /* Test strings */
        $container->register(1);
    }

    /**
     * @depends testContainer
     */
    public function testGetArgumentsNotFoundException($container)
    {
        $container->clear();
        $this->expectException(NotFoundException::class);

        /* Test strings */
        $container->make(Controller::class);
    }

    /**
     * @depends testContainer
     */
    public function testSetArgumentsInvalidArgumentException($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        /* Test strings */
        $container->register(Controller::class)
            ->bindArgument('this_is_not_a_class', null, '__construct')
        ;
    }

    /**
     * @depends testContainer
     */
    public function testPutInvalidArgumentExceptionNotSubclassOf($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        /* Test strings */
        $container->bind(Controller::class, Model::class);
    }

    /**
     * @depends testContainer
     */
    public function testWrongArgumentType($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->bind(View::class);
        $container->bind(Model::class);

        /* Test strings */
        $container->getClosureFor(Controller::class, 'setBoolean', ['value' => 'string']);
    }

    /**
     * @depends testContainer
     */
    public function testClosureForUndefinedMEthod($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->bind(View::class);
        $container->bind(Model::class);

        /* Test strings */
        $container->getClosureFor(Controller::class, 'undefined');
    }

    /**
     * @depends testContainer
     */
    public function testInjectableNotFound($container)
    {
        $container->clear();
        $this->expectException(NotFoundException::class);

        $container->bind(Model::class);
        $container->bind(View::class);

        $container->register(Controller::class)
            ->injectProperty('id')
        ;

        $container->get(Controller::class);
    }

    /**
     * @depends testContainer
     */
    public function testUnkownInjectable($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->register(Controller::class)
            ->injectProperty('unkown')
        ;
    }

    /**
     * @depends testContainer
     */
    public function testUnkownMethodInGetArgument($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->register(Controller::class)
            ->getArgument('model', 'unkown')
        ;
    }

    /**
     * @depends testContainer
     */
    public function testUnkownMethodInGetArguments($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->register(Controller::class)
            ->getArguments('unkown')
        ;
    }

    /**
     * @depends testContainer
     */
    public function testUnkownMethodInBindArgument($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->register(Controller::class)
            ->bindArgument('model', 'value', 'unkown')
        ;
    }

    /**
     * @depends testContainer
     */
    public function testUnkownMethodInHasArgument($container)
    {
        $container->clear();
        $this->expectException(InvalidArgumentException::class);

        $container->register(Controller::class)
            ->hasArgument('model', 'unkown')
        ;
    }

    /**
     * @depends testContainer
     */
    public function testBindArgumentOfNotSameClass($container)
    {
        $container->clear();

        $this->expectException(InvalidArgumentException::class);

        $container->register(Controller::class)
            ->bindArgument(Model::class, View::class, '__construct')
        ;
    }

    /**
     * @depends testContainer
     */
    public function testBindArgumentOfNotClass($container)
    {
        $container->clear();

        $this->expectException(InvalidArgumentException::class);

        $container->register(Controller::class)
            ->bindArgument(Model::class, 'not_a_class', '__construct')
        ;

        $container->get(Controller::class);
    }
}

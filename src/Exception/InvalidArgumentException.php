<?php

namespace Amber\Container\Exception;

use InvalidArgumentException as BaseException;

class InvalidArgumentException extends BaseException
{
    public static function mustBeString()
    {
        throw new self('Key argument must be a non empty string.');
    }

    public static function mustBeClass(string $key)
    {
        throw new self("Argument [{$key}] must be a valid class.");
    }

    public static function mustBeInstanceOf(string $class)
    {
        throw new self("Argument provided is not an instance of [{$class}].");
    }
}

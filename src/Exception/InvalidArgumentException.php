<?php

namespace Amber\Container\Exception;

use InvalidArgumentException as BaseException;

class InvalidArgumentException extends BaseException
{
    public static function mustBeString()
    {
        throw new self('Identifier must be a non empty string.');
    }

    public static function identifierMustBeClass(string $id)
    {
        throw new self("Identifier [{$id}] must be a valid class.");
    }

    public static function mustBeInstanceOf(string $class)
    {
        throw new self("Argument provided is not an instance of [{$class}].");
    }
}

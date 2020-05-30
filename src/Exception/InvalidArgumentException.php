<?php

namespace Amber\Container\Exception;

use Psr\Container\ContainerExceptionInterface;
use InvalidArgumentException as BaseException;

/**
 * @todo SHOULD define new specific exeptions for every exception case.
 */
class InvalidArgumentException extends BaseException implements ContainerExceptionInterface
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

    public static function wrongArgumentType(
        string $paramName,
        string $paramType,
        string $argumentType,
        string $requestedOn
    ) {
        throw new self(
            "Argument for paramater [{$paramName}] must be of type [$paramType]" .
            ", but [{$argumentType}] provided. Requested on [{$requestedOn}]."
        );
    }
}

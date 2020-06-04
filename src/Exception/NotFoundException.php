<?php

namespace Amber\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    public static function throw(string $identifier): self
    {
        throw new self("No entry was found for [{$identifier}] identifier.");
    }
}

<?php

namespace Amber\Container\Contracts;

/**
 * Describes the interface of a service.
 */
interface ServiceInterface
{
    /**
     * Gets the full namespace of the service.
     *
     * @return string The service's name.
     */
    public function getName();

    /**
     * Gets the Service arguments.
     *
     * @param string $method The service's method.
     *
     * @return array
     */
    public function getParameters(string $method = '__construct'): array;

    /**
     * Gets an instance of the Reflection for the current class/closure.
     *
     * @return \Reflector
     */
    public function getReflection(): \Reflector;
}

<?php

namespace Amber\Container;

trait ReflectionTrait
{
    public static function reflectionOf($class)
    {
        /* Instantiate the ReflectionClass */
        return new \ReflectionClass($class);
    }

    public static function getMethodParams($method)
    {
        if ($method instanceof \ReflectionMethod) {
            return $method->getParameters();
        }
    }
}

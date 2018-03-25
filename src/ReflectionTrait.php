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
        if($method instanceof \ReflectionMethod) {
            return $method->getParameters();
        }
        return null;
    }

    public static function getInjectableProperties(\ReflectionClass $reflection)
    {
        foreach ($reflection->getProperties() as $property) {
            if (preg_match("'@inject\s(.*?)[\r\n|\r|\n]'", $property->getDocComment(), $match)) {
                $property->inject = $match[1];

                $injectables[] = $property;
            }
        }

        return $injectables ?? [];
    }

    public static function inject($object, array $injectables)
    {
        foreach ($injectables as $injectable) {
            if ($object instanceof $injectable->class) {
                $object->{$injectable->name} = self::getInstanceOf($injectable->inject);
            }
        }

        return $object;
    }
}

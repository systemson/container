<?php

namespace Amber\Container;

trait MapKeyTrait
{
    public static function bind(string $key, $value)
    {
        if (empty(self::$map)) {
            self::$map = (object) [];
        }

        self::$map->{$key} = $value;

        return true;
    }

    public static function get(string $key)
    {
        return self::$map->{$key} ?? null;
    }

    public static function unbind($key)
    {
        if (isset(self::$map->{$key})) {
            unset(self::$map->{$key});

            return true;
        }

        return false;
    }

    public static function bindMultiple(array $array)
    {
        foreach ($array as $key => $value) {
            self::bind($key, $value);
        }

        return true;
    }

    public static function unbindMultiple(array $array)
    {
        foreach ($array as $key) {
            self::unbind($key);
        }

        return true;
    }

    public static function getParametersFromMap(array $keys = [])
    {
        $params = [];

        foreach ($keys as $key) {

            /* Gets the value from the map */
            $value = self::get($key->name);

            /* Checks if the value is a class to instantiate it */
            if (class_exists($value)) {
                $params[] = self::getInstanceOf($value);

            /* Checks if the value is not null to return it */
            } elseif ($value !== null) {
                $params[] = $value;

            /* Checks if the key is a class to instantiate it */
            } elseif ($key->getClass() && class_exists($key->getClass()->name)) {
                $params[] = self::getInstanceOf($key->getClass()->name);
            }
        }

        return $params;
    }
}

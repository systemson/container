<?php

namespace Amber\Container;

trait MapKeyTrait
{
    public static function set(string $key, string $obj)
    {
        if (empty(self::$map)) {
            self::$map = (object) [];
        }
        self::$map->{$key} = $obj;
    }

    public static function get(string $key = null)
    {
        return self::$map->{$key} ?? null;
    }

    public static function getParametersFromMap(array $keys = [])
    {
        foreach ($keys as $key) {
            if (class_exists($key->name)) {
                $params[] = self::getInstanceOf($key->name);
            } elseif (is_string($key->name)) {
                $params[] = self::get($key->name);
            }
        }

        return $params;
    }
}

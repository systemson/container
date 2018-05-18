<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\NotFoundException;

trait Finder
{
    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @return mixed The value of the item.
     */
    protected function locate($key)
    {
        if ($this->has($key)) {
            return $this->services[$key];
        }

        throw new NotFoundException("No entry was found in for key {$key}");
    }

    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param array $params    Optional. The params needed by the constructor.
     * @param array $arguments Optional. The arguments previously passed to the container.
     *
     * @return array The arguments for the class constructor.
     */
    protected function getArguments($params = [], $arguments = [])
    {
        if (empty($params)) {
            return;
        } elseif (!empty($arguments)) {
            return $arguments;
        }

        foreach ($params as $param) {
            $key = $param->getClass() ? $param->getClass()->name : $param->name;

            /* Gets the value from the map */
            $value = $this->get($key);

            /* Checks if the value is a class to instantiate it */
            if (class_exists($value)) {
                $arguments[] = $this->get($value);

            /* Checks if the value is not null to return it */
            } elseif ($value !== null) {
                $arguments[] = $value;

            /* Checks if the param is a class to instantiate it */
            } elseif ($param->getClass() && class_exists($param->getClass()->name)) {
                $arguments[] = $this->get($param->getClass()->name);
            }
        }

        return $arguments;
    }
}

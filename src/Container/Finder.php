<?php

namespace Amber\Container\Container;

trait Finder
{
    /**
     * Finds an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @return mixed The value of the item.
     */
    protected function locate($key)
    {
        return $this->services[$key] ?? null;
    }

    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param array $params    The params needed by the constructor.
     * @param array $arguments Optional. The arguments previously passed to the container.
     *
     * @return array The arguments for the class constructor.
     */
    protected function getArguments(array $params = [], array $arguments = [])
    {
        if (empty($params)) {
            return;
        } elseif (!empty($arguments)) {
            return $arguments;
        }

        foreach ($params as $param) {
            $key = $param->getClass() ? $param->getClass()->name : $param->name;

            /* Gets the value from the map */
            $arguments[] = $this->get($key);
        }

        return $arguments;
    }
}

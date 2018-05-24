<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\NotFoundException;
use Amber\Container\Service\Service;

trait Finder
{
    /**
     * Returns a Service from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\Exception\NotFoundException
     *
     * @return mixed The value of the item.
     */
    public function locate($key)
    {
        if (!$this->has($key)) {
            throw new NotFoundException("No entry was found for {$key}");
        }

        return $this->services[$key];
    }

    /**
     * Returns and remove a value from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @return mixed The value of the item.
     */
    public function pull($key)
    {
        $value = $this->get($key);

        $this->unbind($key);

        return $value;
    }

    /**
     * Gets the arguments for a Service's constructor.
     *
     * @param array $service   The params needed by the constructor.
     * @param array $arguments Optional. The arguments previously passed to the container.
     *
     * @return array The arguments for the class constructor.
     */
    protected function getArguments(Service $service, array $arguments = [])
    {
        if (!empty($arguments)) {
            return $arguments;
        }

        $params = $service->getParameters();

        if (empty($params)) {
            return;
        }

        foreach ($params as $param) {
            $key = $param->getClass() ? $param->getClass()->name : $param->name;

            /* Gets the value from the map */
            $arguments[] = $this->get($key);
        }

        return $arguments;
    }
}

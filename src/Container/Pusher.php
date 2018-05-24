<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\ContainerException;

trait Pusher
{
    /**
     * Injects dependencies to a service.
     *
     * @param object $instance The object to be injected.
     *
     * @return object The object already injected.
     */
    public function inject($instance)
    {
        $service = $this->locate(get_class($instance));

        foreach ($service->getInjectables() as $property) {
            $instance->{$property->name} = $this->get($property->inject);
        }

        return $instance;
    }

    /**
     * Injects dependencies to an object.
     *
     * @param object $instance   The object to be injected.
     * @param array  $properties An key-value array of properties to inject into the object.
     *
     * @return object The object already injected.
     */
    public function push($instance, array $properties)
    {
        foreach ($properties as $key => $value) {
            if (!property_exists($instance, $key)) {
                throw new ContainerException("Property {$key} does not exists in the current instance");
            } else {
                $instance->{$key} = $this->get($value);
            }
        }

        return $instance;
    }
}

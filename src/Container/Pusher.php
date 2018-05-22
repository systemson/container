<?php

namespace Amber\Container\Container;

trait Pusher
{
    /**
     * Injects dependencies to an object.
     *
     * @params object $object     The object to be injected.
     *
     * @return object The object already injected.
     */
    public function inject($instance)
    {
        $service = $this->locate(get_class($instance));

        $properties = $service->reflection()->injectables;

        foreach ($properties as $property) {
            $instance->{$property->name} = $this->get($property->inject);
        }

        return $instance;
    }

    /**
     * Injects dependencies to an object.
     *
     * @params object $instance     The object to be injected.
     * @params array  $properties   An key-value array of properties to inject into the object.
     *
     * @return object The object already injected.
     */
    public function push($instance, array $properties = [])
    {
        foreach ($properties as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $this->get($value);
            }
        }

        return $instance;
    }
}
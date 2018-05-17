<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\InvalidArgumentException;
use Amber\Common\Validator;

trait Injector
{
    /**
     * Injects dependencies to an object.
     *
     * @params object $object     The object to be injected.
     * @params array  $properties The injectable properties.
     *
     * @return object The object already injected.
     */
    public function inject($object, array $properties = [])
    {
        foreach ($properties as $property) {
            if ($object instanceof $property->class) {
                if ($this->get($property->name)) {
                    $object->{$property->name} = $this->get($property->name);
                } elseif (class_exists($property->inject)) {
                    $object->{$property->name} = $this->getInstanceOf($property->inject);
                } else {
                    throw new ContainerException("Class {$property->inject} does not exists.");
                }
            } else {
                throw new ContainerException("Property {$property->inject} does not belongs to {$property->class}.");
            }
        }

        return $object;
    }
}

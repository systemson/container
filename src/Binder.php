<?php

namespace Amber\Container;


/**
 * Trait to handle the Container's binder
 */
trait Binder
{
    /**
     * Bind an item to the Container's map by a unique key.
     *
     * @param string $key The unique item's key.
     * @param mixed  $key The value of the item.
     *
     * @return bolean true
     */
    public function bind(string $key, $value = null)
    {
        $this->map[$key] = $value ?? $key;

        return true;
    }

    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @return mixed The value of the item.
     */
    public function get($key)
    {
        return $this->map[$key] ?? null;
    }

    /**
     * Removes an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @return bolean true on succes, false on failure.
     */
    public function unbind($key)
    {
        if (isset($this->map[$key])) {
            unset($this->map[$key]);

            return true;
        }

        return false;
    }

    /**
     * Binds multiple items to the Container's map by their unique keys.
     *
     * @param array $items The array of items to add.
     *
     * @return bolean true
     */
    public function bindMultiple(array $items)
    {
        foreach ($items as $key => $value) {
            $this->bind($key, $value);
        }

        return true;
    }

    /**
     * Removes multiple items from the Container's map by their unique keys.
     *
     * @param array $items The array of items to remove.
     *
     * @return bolean true
     */
    public function unbindMultiple(array $array)
    {
        foreach ($array as $key) {
            $this->unbind($key);
        }

        return true;
    }

    /**
     * Get the arguments for the instantiation of the class.
     *
     * @param array $params    The params needed by the constructor.
     * @param array $arguments The arguments previously passed to the container.
     *
     * @return array The arguments for the class constructor.
     */
    public function getArguments($params = [], $arguments = [])
    {
        if(empty($params)) {
           return null;
        } elseif(!empty($arguments)) {
            return $arguments;
        }

        foreach ($params as $param) {

            $key = $param->getClass() ? $param->getClass()->name : $param->name;

            /* Gets the value from the map */
            $value = $this->get($key);

            /* Checks if the value is a class to instantiate it */
            if (class_exists($value)) {

                $arguments[] = $this->getInstanceOf($value);

            /* Checks if the value is not null to return it */
            } elseif ($value !== null) {

                $arguments[] = $value;

            /* Checks if the param is a class to instantiate it */
            } elseif ($param->getClass() && class_exists($param->getClass()->name)) {

                $arguments[] = $this->getInstanceOf($param->getClass()->name);
            }
        }

        return $arguments;
    }

    /**
     * Injects depedencies to an object.
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
                }
            }
        }

        return $object;
    }
}

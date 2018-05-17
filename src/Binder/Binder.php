<?php

namespace Amber\Container\Binder;

use Amber\Cache\Exception\InvalidArgumentException;
use Amber\Common\Validator;
use Psr\Container\ContainerInterface;

/**
 * Class to handle the Container's binder.
 */
class Binder implements ContainerInterface
{
    use Validator;

    public $map = [];

    /**
     * Bind an item to the Container's map by a unique key.
     *
     * @param string $key The unique item's key.
     * @param mixed  $key The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return bolean true
     */
    public function bind($key, $value = null)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        $this->map[$key] = $value ?? $key;

        return true;
    }

    /**
     * Gets an item from the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\InvalidArgumentException
     *
     * @return mixed The value of the item.
     */
    public function get($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        return $this->map[$key] ?? null;
    }

    /**
     * Checks for an item on the Container's map by its unique key.
     *
     * @param string $key The unique item's key.
     *
     * @throws Amber\Container\InvalidArgumentException
     *
     * @return bool
     */
    public function has($key)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        return isset($this->map[$key]) ?? false;
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
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

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

            /* Throws an InvalidArgumentException on invalid type. */
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

            /* Throws an InvalidArgumentException on invalid type. */
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
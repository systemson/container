<?php

namespace Amber\Container\Container;

use Amber\Container\Exception\InvalidArgumentException;

/**
 * Class to handle the Container's binder.
 */
abstract class Binder extends SimpleBinder
{
    use BinderExtras;

    /**
     * Binds and gets an item from the Container's map by a unique key.
     *
     * @param string $key   The unique item's key.
     * @param mixed  $value The value of the item.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *
     * @return mixed The value of the item.
     */
    public function bindAndGet($key, $value)
    {
        /* Throws an InvalidArgumentException on invalid type. */
        if (!$this->isString($key)) {
            throw new InvalidArgumentException('Key argument must be a non empty string');
        }

        $this->bind($key, $value);

        return $this->get($key, $value);
    }

    /**
     * Binds and gets multiple items from the Container's map by their unique keys.
     *
     * @param array $items An array of items to get.
     *
     * @return array The values of the items.
     */
    public function bindAndGetMultiple(array $items)
    {
        $result = [];

        foreach ($items as $key => $value) {
            $result[] = $this->bindAndGet($key, $value);
        }

        return $result;
    }
}

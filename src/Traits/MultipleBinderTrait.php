<?php

namespace Amber\Container\Traits;

trait MultipleBinderTrait
{
    /**
     * Binds multiple items to the Container's map by their unique keys.
     *
     * @param array $items An array of key => value items to bind.
     *
     * @return bool true
     */
    final public function bindMultiple(array $items)
    {
        foreach ($items as $identifier => $value) {
            $identifier = is_int($identifier) ? $value : $identifier;

            $this->bind($identifier, $value);
        }

        return true;
    }

    /**
     * Gets multiple items from the Container's map by their unique keys.
     *
     * @param array $items An array of items to get.
     *
     * @return array The values of the items.
     */
    final public function getMultiple(array $items)
    {
        foreach ($items as $identifier) {
            $services[] = $this->get($identifier);
        }

        return $services;
    }

    /**
     * Removes multiple items from the Container's map by their unique keys.
     *
     * @param array $items An array of items to remove.
     *
     * @return bool true
     */
    final public function unbindMultiple(array $items)
    {
        foreach ($items as $identifier) {
            $this->unbind($identifier);
        }

        return true;
    }
}

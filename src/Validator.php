<?php

namespace Amber\Container;

trait Validator
{
    /**
     * Check if the specified key is valid.
     *
     * @param string $key The key to validate.
     *
     * @throws Amber\Container\InvalidArgumentException
     *
     * @return bool True if the specified key is valid.
     */
    protected function validate($key)
    {
        if (!is_string($key) || $key === '') {
            throw new InvalidArgumentException('Key should be a non empty string');
        }

        return true;
    }
}

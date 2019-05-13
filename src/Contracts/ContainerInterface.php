<?php

namespace Amber\Container\Contracts;

use Psr\Container\ContainerInterface as PsrInterface;

/**
 * Describes the interface of a container that exposes methods to read its entries.
 */
interface ContainerInterface extends PsrInterface
{
    /**
     * Binds an entry to the container by its identifier.
     *
     * When no $value is provided $id must be a valid class.
     * When $id and $value are classes, $value must be a subclass of $id, or the same class.
     *
     * @param string $id    The entry's identifier.
     * @param mixed  $value Optional. The entry's value.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *         Identifier must be a non empty string.
     *         Identifier [$id] must be a valid class.
     *         Class [$value] must be a subclass of [$id], or the same.
     *
     * @return bool True on success. False if identifier already exists.
     */
    public function bind($id, $value = null): bool;

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id The entry's identifier.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *         Identifier must be a non empty string.
     * @throws Amber\Container\Exception\NotFoundExceptionInterface
     *         No entry was found for [$id] identifier.
     * @throws Amber\Container\Exception\ContainerExceptionInterface
     *         Error while retrieving the entry.
     *
     * @return mixed The entry.
     */
    public function get($id);

    /**
     * Wether an entry is present in the container.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id The entry's identifier.
     *
     * @return bool
     */
    public function has($id): bool;

    /**
     * Unbinds an entry from the container by its identifier.
     *
     * @param string $id The entry's identifier.
     *
     * @return bool True on success. False if identifier doesn't exists.
     */
    public function unbind($id): bool;

    /**
     * Binds and Gets an instance of the specified class from the container.
     *
     * @param string $class The entry's class.
     *
     * @throws Amber\Container\Exception\InvalidArgumentException
     *         Identifier [$id] must be a valid class.
     * @throws Amber\Container\Exception\ContainerExceptionInterface
     *         Error while retrieving the entry.
     *
     * @return mixed The entry.
     */
    public function make(string $class);
}

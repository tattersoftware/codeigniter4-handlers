<?php

namespace Tatter\Handlers\Interfaces;

/**
 * Interface for anything discoverable by Handlers. Ensures that
 * attributes can be properly read. In addition to these methods
 * the class must also support initialization without parameters.
 *
 * Note:
 * This interface will always be compatible with CodeIgniter\Entity.
 */
interface HandlerInterface
{
    /**
     * Magic method to allow retrieval of attributes.
     *
     * @return mixed
     */
    public function __get(string $key);

    /**
     * Returns true if a property exists named $key.
     */
    public function __isset(string $key): bool;

    /**
     * Returns a handler's attributes as an array.
     * Note: parameters are present to maintain compatibility with CodeIgniter\Entity
     * but are not used by Tatter\Handlers.
     *
     * @param bool $onlyChanged If true, only return values that have changed since object creation
     * @param bool $cast        If true, properties will be casted.
     * @param bool $recursive   If true, inner entities will be casted as array as well.
     */
    public function toArray(bool $onlyChanged = false, bool $cast = true, bool $recursive = false): array;
}

<?php

namespace Tatter\Handlers\Traits;

trait HandlerTrait
{
    /**
     * Magic method to allow retrieval of attributes.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Returns true if a property exists named $key.
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Returns a handler's attributes as an array.
     * Note: parameters are present to maintain compatibility with CodeIgniter\Entity
     * but are not used by Tatter\Handlers.
     *
     * @param bool $onlyChanged If true, only return values that have changed since object creation
     * @param bool $cast        If true, properties will be casted.
     * @param bool $recursive   If true, inner entities will be casted as array as well.
     */
    public function toArray(bool $onlyChanged = false, bool $cast = true, bool $recursive = false): array
    {
        return $this->attributes;
    }
}

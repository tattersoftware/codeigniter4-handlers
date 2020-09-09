<?php namespace Tatter\Handlers\Traits;

trait HandlerTrait
{
	/**
	 * Magic method to allow retrieval of attributes.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get(string $key)
	{
		return $this->attributes[$key] ?? null;
	}

	/**
	 * Returns true if a property exists named $key.
	 *
	 * @param string $key
	 *
	 * @return boolean
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
	 * @param boolean $onlyChanged If true, only return values that have changed since object creation
	 * @param boolean $cast        If true, properties will be casted.
	 * @param boolean $recursive   If true, inner entities will be casted as array as well.
	 *
	 * @return array
	 */
	public function toArray(bool $onlyChanged = false, bool $cast = true, bool $recursive = false): array
	{
		return $this->attributes;
	}
}

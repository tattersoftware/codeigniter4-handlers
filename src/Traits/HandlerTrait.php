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
}

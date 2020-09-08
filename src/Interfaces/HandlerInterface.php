<?php namespace Tatter\Handlers\Interfaces;

interface HandlerInterface
{
	/**
	 * Magic method to allow retrieval of attributes.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get(string $name);
}

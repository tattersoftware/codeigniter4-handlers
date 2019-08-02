<?php namespace Tatter\Handlers\Interfaces;

interface AdapterInterface
{
	// Magic wrapper for getting attribute values, supplied by AdapterTrait
	public function __get(string $name);
}

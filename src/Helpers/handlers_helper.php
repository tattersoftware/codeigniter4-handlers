<?php

use Config\Services;
use Tatter\Handlers\Handlers;

if (! function_exists('handlers'))
{
	/**
	 * Returns the Handlers service set to the specified path.
	 */
	function handlers(string $path = ''): Handlers
	{
		return $path ? Services::handlers()->setPath($path) : Services::handlers();
	}
}

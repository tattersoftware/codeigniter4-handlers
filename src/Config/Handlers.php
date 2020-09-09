<?php namespace Tatter\Handlers\Config;

use CodeIgniter\Config\BaseConfig;

class Handlers extends BaseConfig
{
    /**
     * Classes to ignore across all handlers.
     *
     * @var array<string>
     */
	public $ignoredClasses = [];

    /**
     * Paths to check during discovery.
     *
     * @var array<string>
     */
	public $discoveryPaths = [];
}

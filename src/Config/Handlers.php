<?php namespace Tatter\Handlers\Config;

use CodeIgniter\Config\BaseConfig;

class Handlers extends BaseConfig
{
    /**
     * Whether to continue instead of throwing exceptions.
     *
     * @var bool
     */
	public $silent = true;

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

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
     * Paths to check during preemptive discovery.
     *
     * @var array<string>
     */
	public $discoveryPaths = [];

    /**
     * Number of seconds to cache discovered handlers.
     *
     * @var int
     */
	public $cacheDuration = DAY;
}

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
	 * Paths to check during automatic discovery.
	 *
	 * @var array<string>
	 */
	public $autoDiscover = [];

	/**
	 * Number of seconds to cache discovered handlers.
	 * Null disables caching
	 *
	 * @var integer|null
	 */
	public $cacheDuration = DAY;
}

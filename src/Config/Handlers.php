<?php namespace Tatter\Handlers\Config;

use CodeIgniter\Config\BaseConfig;

class Handlers extends BaseConfig
{
	// Whether to continue instead of throwing exceptions
	public $silent = true;
	
	// Session variable to check for a logged-in user ID
	public $userSource = 'logged_in';
	
	// Relative directory in each namespace to check for supported handlers
	public $directory = '/Handlers';
}

<?php namespace Config;

/***
*
* This file contains example values to alter default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Handlers.php
*	2. Change any values
*	3. Remove any lines to fallback to defaults
*
***/

class HandlersConfig extends \Tatter\Handlers\Config\HandlersConfig
{
	// whether to continue instead of throwing exceptions
	public $silent = true;
	
	// the session variable to check for a logged-in user ID
	public $userSource = 'logged_in';
	
	// Config file in each namespace to check for supported handlers
	public $configFile = 'Handlers';
}

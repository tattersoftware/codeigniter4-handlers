<?php namespace CIModuleTests\Support\Config;

use CodeIgniter\Config\BaseConfig;

class Handlers extends BaseConfig
{
	// Directory to search across namespaces for supported handlers
	public $directory = 'Factories';
	
	// Model used to track handlers
	public $model = '\CIModuleTests\Support\Models\FactoryModel';
}

<?php namespace Tests\Support\Config;

use CodeIgniter\Config\BaseConfig;
use Tests\Support\Models\FactoryModel;

class Handlers extends BaseConfig
{
	// Directory to search across namespaces for supported handlers
	public $directory = 'Factories';
	
	// Model used to track handlers
	public $model = FactoryModel::class;
}

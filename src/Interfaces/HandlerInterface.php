<?php namespace Tatter\Handlers\Interfaces;

use CodeIgniter\Config\BaseConfig;

interface HandlerInterface
{
	// Magic wrapper for getting attribute values, supplied by AdapterTrait
	public function __get(string $name);
	
	// Function to inject a library's config
	public function setConfig(BaseConfig $config);
}

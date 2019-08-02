<?php namespace Tatter\Handlers\Handlers;

use CodeIgniter\Config\BaseConfig;

class BaseHandler
{
	public $config;
	public $attributes;
	
	// Magic wrapper for getting attribute values
    public function __get(string $name)
    {
		return $this->attributes[$name];
    }
	
	public function setConfig(BaseConfig $config)
	{		
		$this->config = $config;			
	}
}

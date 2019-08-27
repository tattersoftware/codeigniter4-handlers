<?php namespace Tatter\Handlers\Handlers;

use CodeIgniter\Config\BaseConfig;

class BaseHandler
{
	public    $config;
	public    $attributes;
	protected $errors = [];
	
	// Magic wrapper for getting attribute values
    public function __get(string $name)
    {
		return $this->attributes[$name] ?? null;
    }
	
	public function setConfig(BaseConfig $config)
	{		
		$this->config = $config;			
	}
	
	// Get any errors from latest operation
	public function getErrors(): array
	{
		return $this->errors;
	}
}

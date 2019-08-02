<?php namespace Tatter\Handlers\Traits;

/*** CLASS ***/
trait AdaptersTrait
{
	public $config;
	
	public function __construct()
	{		
		$this->config = config('Exports');			
		$this->model  = new ExportModel();
	}
	
	// Magic wrapper for getting attribute values
    public function __get(string $name)
    {
		return $this->attributes[$name];
    }
}

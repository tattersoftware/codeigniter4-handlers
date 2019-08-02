<?php namespace Tatter\Handlers;

use CodeIgniter\Config\BaseConfig;
use Tatter\Handlers\Exceptions\HandlersException;
use Tatter\Handlers\Interfaces\HandlerInterface;

class Handlers
{
	/**
	 * The configuration instance.
	 *
	 * @var \Tatter\Handlers\Config\Handlers
	 */
	protected $config;
	
	/**
	 * Array error messages assigned on failure
	 *
	 * @var array
	 */
	protected $errors;
	
	
	// initiate library
	public function __construct(BaseConfig $config)
	{		
		// Save the configuration
		$this->config = $config;
	}
	
	// Return any error messages
	public function getErrors()
	{
		return $this->errors;
	}

	// Scan namespaces for handler definition config files
	public function findConfigs()
	{
		$configs = [];
/*
		// Get Config/Handlers.php from all namespaces
		$locator = service('locator');
		$files = $locator->search('Config/Handlers.php');
*/		
		// Get all namespaces from the autoloader
		$namespaces = service('autoloader')->getNamespace();
		
		// Check each namespace
		foreach ($namespaces as $namespace => $paths):
			// Look for Config/Handlers.php
			$config = config($namespace . '/' . $this->config->configFile);
			if (empty($config)):
				continue;
			endif;
			
			// Validate the config file
			$class = get_class($config);
			if (! isset($config->directory, $config->model)):
				if ($this->config->silent):
					$this->errors[] = lang('Handlers.invalidFormat', [$class]);
					continue;
				else:
					throw HandlersException::forInvalidFormat($class);
				endif;
			endif;
			
			// Save it
			$configs[] = $class;
		endforeach;
		
		return $configs;
	}
	
	// Scan for any supported handlers for a given config
	public function findHandlers($configClass)
	{
		$handlers = [];
		
		// Get an instance of the config
		$config = new $configClass();

		// Get all namespaces from the autoloader
		$namespaces = service('autoloader')->getNamespace();
		$locator    = service('locator');
		
		// Scan each namespace for handlers
		foreach ($namespaces as $namespace => $paths):

			// Get any files in the defined directory for this namespace
			$files = $locator->listNamespaceFiles($namespace, $config->directory);
			foreach ($files as $file):

				// Skip non-PHP files
				if (substr($file, -4) !== '.php'):
					continue;
				endif;
				
				// Get the namespaced class name
				$name = basename($file, '.php');
				$class = $namespace . '\\' . $config->directory . '\\' . $name;
				
				// Try to load the file
				try {
					require_once $file;
				} catch (Exception $e) {
					if ($this->config->silent):
						$this->errors[] = lang('Handlers.loadFail', [$file, $e]);
						continue;
					else:
						throw HandlersException::forLoadFail($file, $e);
					endif;
				}

				// Validate the class
				if (! class_exists($class, false)):
					if ($this->config->silent):
						$this->errors[] = lang('Handlers.missingClass', [$file, $class]);
						continue;
					else:
						throw HandlersException::forMissingClass($file, $class);
					endif;
				endif;
				
				// Get the instance and validate the necessary properties
				$instance = new $class();
				if (! $instance instanceof HandlerInterface):
					if ($this->config->silent):
						$this->errors[] = lang('Handlers.invalidFormat', [$class]);
						continue;
					else:
						throw HandlersException::forInvalidFormat($class);
					endif;
				endif;
				
				// Save it
				$handlers[] = $class;
				
			endforeach;
		endforeach;
		
		return $handlers;
	}
}

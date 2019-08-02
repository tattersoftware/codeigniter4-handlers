<?php namespace Tatter\Handlers;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Services;
use Tatter\Handlers\Exceptions\HandlersException;

class Handlers
{
	/**
	 * The configuration instance.
	 *
	 * @var \Tatter\Handlers\Config\Handlers
	 */
	protected $config;
	
	/**
	 * Array of discovered handlers
	 *
	 * @var array
	 */
	protected $handlers;
	
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
		
		// Check for cached version of discovered handlers
		$this->handlers = cache('handlers');
	}
	
	// Return any error messages
	public function getErrors()
	{
		return $this->errors;
	}

	// Returns an array of discovered handlers
	public function getHandlers()
	{
		$this->discover();
		return $this->handlers;
	}
	
	// Scan for any supported adapters for a given handler
	public function getAdapters($handlerClass)
	{
		$adapters = [];
		
		// Get an instance of the handler
		$handler = new $handlerClass();

		// Get all namespaces from the autoloader
		$locator = Services::locator(true);
		$namespaces = Services::autoloader()->getNamespace();
		
		// Scan each namespace for handlers
		foreach ($namespaces as $namespace => $paths):

			// Get any files in the adapters directory for this namespace
			$files = $locator->listNamespaceFiles($namespace, $handler->adaptersDirectory);
			foreach ($files as $file):
			
				// Skip non-PHP files
				if (substr($file, -4) !== '.php'):
					continue;
				endif;
				
				// Get the namespaced class name
				$name = basename($file, '.php');
				$class = $namespace . '\\' . $handler->adaptersDirectory . '\\' . $name;
				
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
				if (! isset($instance->attributes)):
					if ($this->config->silent):
						$this->errors[] = lang('Handlers.invalidFormat', [$file]);
						continue;
					else:
						throw HandlersException::forInvalidFormat($file);
					endif;
				endif;
				
				// Save it
				$adapters[] = $class;
				
			endforeach;
		endforeach;
		
		return $adapaters;
	}
	
	// Ensures all supported handlers have been located and loaded
	protected function discover()
	{
		// Check if already discovered
		if (! is_null($this->handlers)):
			return true;
		endif;
		
		// Check for a cached copy
		if ($cached = cache('handlers')):
			$this->handlers = $cached;
			return true;
		endif;

		// Get all namespaces from the autoloader
		$locator = Services::locator(true);
		$namespaces = Services::autoloader()->getNamespace();
		
		// Scan each namespace for handlers
		foreach ($namespaces as $namespace => $paths):

			// Get any files in the configured directory for this namespace
			$files = $locator->listNamespaceFiles($namespace, $this->config->directory);
			foreach ($files as $file):
			
				// Skip non-PHP files
				if (substr($file, -4) !== '.php'):
					continue;
				endif;
				
				// Get the namespaced class name
				$name = basename($file, '.php');
				$class = $namespace . '\\' . $this->config->directory . '\\' . $name;
				
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
				if (! isset($instance->directory, $instance->model)):
					if ($this->config->silent):
						$this->errors[] = lang('Handlers.invalidFormat', [$file]);
						continue;
					else:
						throw HandlersException::forInvalidFormat($file);
					endif;
				endif;
				
				// Save it
				$this->handlers[] = $class;

			endforeach;
		endforeach;
		
		// Cache the results
		cache()->save('handlers', $this->handlers, 300);
	}
}

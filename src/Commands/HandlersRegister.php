<?php namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class HandlersRegister extends BaseCommand
{
    protected $group       = 'Handlers';
    protected $name        = 'handlers:register';
    protected $description = 'Locate supported handlers and add them to the database';
	protected $usage       = 'handlers:register';
	protected $arguments   = [];

	public function run(array $params = [])
    {
    	// Load the library
		$lib = service('handlers');
		
		// Fetch all supported configs
		$configs = $lib->findConfigs();
		if (empty($configs)):
			CLI::write('WARNING: No handler config files detected!', 'yellow');
			return;
		endif;
		
		// Process each handler
		foreach ($configs as $configClass):
		
			// Scan for supported handlers
			$handlers = $lib->findHandlers($configClass);
			if (empty($handlers)):
				// Check for errors
				if ($errors = $lib->getErrors()):
					foreach ($errors as $error):
						CLI::write($error, 'red');
					endforeach;
				else:
					CLI::write('No handlers detected for config file: ' . $configClass, 'yellow');
				endif;
											
				continue;
			endif;
			
			// Get an instance of the model
			$config = new $configClass();
			$model = new $config->model();

			// Load each handler
			foreach ($handlers as $handlerClass):

				// Get the attributes from the handler itself
				$handler = new $handlerClass();
				$row = $handler->attributes;
				$row['class'] = $handlerClass;

				// Check for an existing registration
				if ($existing = $model->where('uid', $row['uid'])->first()):
					// Update it
					$model->update($existing->id, $row);
				else:
					// Create a new registration
					$handlerId = $model->insert($row);
					CLI::write("New handler registered for {$configClass}: {$handlerClass}", 'green');
				endif;
				
			endforeach;
			
		endforeach;
		
		$this->call('handlers:list');
	}
}

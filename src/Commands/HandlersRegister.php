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
			CLI::write('ERROR: No handle config files detected!', 'red');
			return;
		endif;
		
		// Process each handler
		foreach ($configs as $configClass):
		
			// Scan for supported handlers
			$handlers = $lib->findHandlers($configClass);
			if (empty($handlers)):
				CLI::write('No handlers detected for config file: ' . $configClass, 'yellow');
				continue;
			endif;
			
			// Get an instance of the model
			$config = config($configClass);
			$model = new $config->model();
			
			// Load each handler
			foreach ($handlers as $handlerClass):

				// Get the attributes from the adapter itself
				$handler = new $handlerClass();
				$row = $handler->attributes;
				$row['class'] = $handlerClass;

				// Check for an existing adapter registration
				if ($handlerId = $model->where('uid', $row['uid'])->first()):
					// Update it
					$model->where('uid', $row->uid)->update($row);
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

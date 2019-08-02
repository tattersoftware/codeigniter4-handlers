<?php namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AdaptersRegister extends BaseCommand
{
    protected $group       = 'Handlers';
    protected $name        = 'adapters:register';
    protected $description = 'Locate supported handlers and add their adapters to the database';
	protected $usage       = 'adapters:register';
	protected $arguments   = [];

	public function run(array $params = [])
    {
    	// Load the library
		$lib = service('handlers');
		
		// Fetch all supported handlers
		$handlers = $lib->getHandlers();
		if (empty($handlers)):
			CLI::write('ERROR: No handlers detected!', 'red');
			return;
		endif;
		
		// Process each handler
		foreach ($handlers as $handlerClass):
		
			// Scan for supported adapters
			$adapters = $lib->getAdapters($handlerClass);
			if (empty($adapters)):
				CLI::write('No adapaters detected for handler: ' . $handlerClass, 'yellow');
				continue;
			endif;
			
			// Get an instance of this handler's model
			$handler = new $handlerClass();
			$model = new $handler->{model}();
			
			// Load each adapter
			foreach ($adapters as $adapterClass):

				// Get the attributes from the adapter itself
				$adapter = new $adapterClass();
				$row = $adapter->attributes;
				$row['class'] = $adapterClass;

				// Check for an existing adapter registration
				if ($adapterId = $model->where('uid', $row->uid)->first()):
					// Update it
					$model->where('uid', $row->uid)->update($row);
				else:
					// Create a new registration
					$adapterId = $model->insert($row);
					CLI::write("New adapter registered for {$handlerClass}: {$adapterClass}", 'green');
				endif;
				
			endforeach;
			
		endforeach;
		
		$this->call('adapters:list');
	}
}

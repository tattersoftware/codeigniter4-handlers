<?php namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class HandlersList extends BaseCommand
{
    protected $group       = 'Handlers';
    protected $name        = 'handlers:list';
    protected $description = 'List all supported handlers';
	protected $usage       = 'handlers:list';
	protected $arguments   = [];

	public function run(array $params = [])
    {
    	// Load the library
		$lib = service('handlers');
		
		// Fetch all config classes to search
		$configs = $lib->findConfigs();
		if (empty($configs)):
			CLI::write('ERROR: No config files detected!', 'red');
			return;
		endif;
		
		// Process each handler
		foreach ($configs as $configClas):
			CLI::write($configClas, 'black', 'light_gray');
			
			// Scan for supported handlers
			$handlers = $lib->findHandlers($configClas);
			if (empty($handlers)):
				CLI::write('No handlers detected.', 'yellow');
				continue;
			endif;
			
			// Display each handler
			foreach ($handlers as $handlerClass):
				CLI::write($handlerClass);
			endforeach;
		endforeach;
	}
}

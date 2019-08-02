<?php namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AdaptersList extends BaseCommand
{
    protected $group       = 'Handlers';
    protected $name        = 'adapters:list';
    protected $description = 'List all handlers and their supported adapters';
    
	protected $usage     = 'adapters:list';
	protected $arguments = [ ];

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
			CLI::write($handlerClass, 'black');
			
			// Scan for supported adapters
			$adapters = $lib->getAdapters($handlerClass);
			if (empty($adapters)):
				CLI::write('No adapaters detected.', 'yellow');
				continue;
			endif;
			
			// Load each adapter
			foreach ($adapters as $adapterClass):
				CLI::write($adapterClass);
			endforeach;
		endforeach;
	}
}

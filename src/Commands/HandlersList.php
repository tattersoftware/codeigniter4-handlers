<?php

namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Handlers\Handlers;

class HandlersList extends BaseCommand
{
	protected $group       = 'Housekeeping';
	protected $name        = 'handlers:list';
	protected $description = 'List all discovered handlers';
	protected $usage       = 'handlers:list';

	public function run(array $params = [])
	{
		// Load the library
		$handlers = new Handlers();

		// Make sure auto-discovery is enabled
		if (empty($handlers->getConfig()->autoDiscover))
		{
			CLI::write('No paths are set for automatic discovery. See the config file for Tatter\Handlers.', 'yellow');

			return;
		}

		// Process each path
		foreach ($handlers->getConfig()->autoDiscover as $path)
		{
			$handlers->setPath($path);
			CLI::write($path, 'black', 'light_gray');

			if (! $classes = $handlers->findAll())
			{
				CLI::write('No handlers detected.', 'yellow');

				continue;
			}

			// Display each class
			foreach ($classes as $class)
			{
				CLI::write($class);
			}
		}
	}
}

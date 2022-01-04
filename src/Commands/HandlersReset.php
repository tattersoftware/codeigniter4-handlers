<?php

namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Handlers\Handlers;

class HandlersReset extends BaseCommand
{
    protected $group       = 'Housekeeping';
    protected $name        = 'handlers:reset';
    protected $description = 'Clear cached versions of discovered handlers';
    protected $usage       = 'handlers:reset';

    public function run(array $params = [])
    {
        // Load the library
        $handlers = new Handlers();

        // Make sure auto-discovery is enabled
        if (empty($handlers->getConfig()->autoDiscover)) {
            CLI::write('No paths are set for automatic discovery. See the config file for Tatter\Handlers.', 'yellow');

            return;
        }

        // Process each path
        foreach ($handlers->getConfig()->autoDiscover as $path) {
            $handlers->setPath($path);
            $handlers->cacheClear();
        }

        CLI::write('All cached handlers cleared!', 'green');
    }
}

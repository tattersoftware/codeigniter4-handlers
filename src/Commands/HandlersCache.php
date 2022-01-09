<?php

namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Handlers\Factories\FactoryFactory;
use Throwable;

class HandlersCache extends BaseCommand
{
    protected $group       = 'Housekeeping';
    protected $name        = 'handlers:cache';
    protected $description = 'Discovers and caches all handlers';
    protected $usage       = 'handlers:cache';

    public function run(array $params = [])
    {
        // Load the Factories of Factories
        $factories = new FactoryFactory();

        // Make sure caching is enabled
        if ($factories->getConfig()->cacheDuration === null) {
            CLI::error('Handler caching is disabled by the Tatter\Handlers Config file.');

            return;
        }

        // Create each Factory, triggering its discovery and subsequent caching
        $count  = 0;
        $errors = 0;

        foreach ($factories->findAll() as $factory) {
            try {
                new $factory();
                $count++;
            }
            // @phpstan-ignore-next-line
            catch (Throwable $e) {
                $errors++;
                $this->showError($e);
            }
        }

        if ($count === 0) {
            CLI::error('No factories discovered!');

            return;
        }

        // @phpstan-ignore-next-line
        if ($errors > 0) {
            CLI::error('Total errors encountered: ' . $errors);
        }

        CLI::write('Total factories processed: ' . $count, 'green');
    }
}

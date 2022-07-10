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

    /**
     * @return void
     */
    public function run(array $params = [])
    {
        // Make sure caching is enabled
        if (config('Handlers')->cacheDuration === null) {
            CLI::error('Handler caching is disabled by the Tatter\Handlers Config file.');

            return;
        }

        // Use the Factory of Factories to locate compatible Factories
        $count  = 0;
        $errors = 0;

        foreach (FactoryFactory::findAll() as $factory) {

            // Run each factory's discover to trigger its cache commit
            try {
                $factory::findAll();
                $count++;
            } catch (Throwable $e) {
                $errors++;
                $this->showError($e);
            }
        }

        if ($count === 0) {
            CLI::error('No factories discovered!');

            return;
        }

        if ($errors > 0) {
            CLI::error('Total errors encountered: ' . $errors);
        }

        CLI::write('Total factories processed: ' . $count, 'green');
    }
}

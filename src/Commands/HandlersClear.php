<?php

namespace Tatter\Handlers\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class HandlersClear extends BaseCommand
{
    protected $group       = 'Housekeeping';
    protected $name        = 'handlers:clear';
    protected $description = 'Clears cached versions of discovered handlers';
    protected $usage       = 'handlers:clear';

    /**
     * @return void
     */
    public function run(array $params = [])
    {
        $count = cache()->deleteMatching('handlers-*');

        CLI::write('Total cached factories cleared: ' . $count, 'green');
    }
}

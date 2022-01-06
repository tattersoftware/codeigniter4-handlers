<?php

namespace Tests\Support;

use Tatter\Handlers\BaseManager;

class FactoryManager extends BaseManager
{
    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return 'Factories';
    }
}

<?php

namespace Tests\Support\Factories;

use Tatter\Handlers\BaseFactory;

class CarFactory extends BaseFactory
{
    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return 'Cars';
    }
}

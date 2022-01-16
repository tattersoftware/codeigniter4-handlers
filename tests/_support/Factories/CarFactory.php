<?php

namespace Tests\Support\Factories;

use Tatter\Handlers\BaseFactory;
use Tatter\Handlers\Interfaces\HandlerInterface;

class CarFactory extends BaseFactory implements HandlerInterface
{
    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return 'Cars';
    }
}

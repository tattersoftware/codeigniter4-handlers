<?php

namespace Tatter\Handlers\Factories;

use Tatter\Handlers\BaseFactory;
use Tatter\Handlers\Interfaces\HandlerInterface;

/**
 * Factory Factory Class
 *
 * Used to discover other Factory classes
 * which are set up as handlers themselves.
 */
class FactoryFactory extends BaseFactory implements HandlerInterface
{
    /**
     * Use Factories-style class basenames to
     * guesstimate a good handlerId.
     */
    public static function handlerId(): string
    {
        return 'factory';
    }

    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return 'Factories';
    }
}

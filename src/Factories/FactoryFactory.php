<?php

namespace Tatter\Handlers\Factories;

use Tatter\Handlers\BaseFactory;

/**
 * Factory Factory Class
 *
 * Used to discover other Factory classes
 * which are set up as handlers themselves.
 */
class FactoryFactory extends BaseFactory
{
    public static function handlerId(): string
    {
        return 'factory';
    }

    public function getPath(): string
    {
        return 'Factories';
    }
}

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
    public static function handlerId(): string
    {
        return 'factories';
    }

    public static function attributes(): array
    {
        return [
            'name' => 'Factory of Factories',
        ];
    }

    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return 'Factories';
    }
}

<?php

namespace Tests\Support\Managers;

use Tatter\Handlers\BaseManager;
use Tatter\Handlers\Interfaces\HandlerInterface;

class FactoryManager extends BaseManager implements HandlerInterface
{
    public static function handlerId(): string
    {
        return 'factories';
    }

    public static function attributes(): array
    {
        return [
            'name' => 'Manager of Factories',
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

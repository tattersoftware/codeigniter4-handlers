<?php

namespace Tests\Support\Factories;

use Tatter\Handlers\BaseFactory;
use Tatter\Handlers\Interfaces\HandlerInterface;

class CarFactory extends BaseFactory implements HandlerInterface
{
    public static function handlerId(): string
    {
        return 'cars';
    }

    public static function attributes(): array
    {
        return [
            'name' => 'Factory of Cars',
        ];
    }

    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
        return 'Cars';
    }
}

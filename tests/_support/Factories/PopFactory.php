<?php

namespace Tests\Support\Factories;

use Tatter\Handlers\Interfaces\HandlerInterface;

class PopFactory implements HandlerInterface
{
    /**
     * Returns this handler's identifier,
     * unique per path.
     */
    public static function handlerId(): string
    {
        return 'pop';
    }

    /**
     * Returns the array of path-specific attributes.
     *
     * @return array<string, scalar>
     */
    public static function attributes(): array
    {
        return [
            'group'   => 'East',
            'name'    => 'Pop Factory',
            'uid'     => 'pop',
            'summary' => 'Makes pop',
            'cost'    => 1,
            'list'    => 'five,six',
        ];
    }

    public function process()
    {
        return 'pop';
    }

    public function register()
    {
        session()->set(['didRegister' => true]);
    }
}

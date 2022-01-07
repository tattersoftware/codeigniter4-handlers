<?php

namespace Tests\Support\Cars;

use Tatter\Handlers\Interfaces\HandlerInterface;

class PopCar implements HandlerInterface
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
            'name'    => 'Pop Car',
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

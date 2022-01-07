<?php

namespace Tests\Support\Cars;

use Tests\Support\Interfaces\DummyInterface;

class BadCar implements DummyInterface
{
    /**
     * Returns this handler's identifier,
     * unique per path.
     */
    public static function handlerId(): string
    {
        return 'bad';
    }

    /**
     * Returns the array of path-specific attributes.
     *
     * @return array<string, scalar>
     */
    public static function attributes(): array
    {
        return [
            'group'   => 'South',
            'name'    => 'Bad Car',
            'uid'     => 'bad',
            'summary' => 'This car does not implement HandlerInterface as it should',
        ];
    }

    public function process()
    {
        return 'poop';
    }
}

<?php

namespace Tests\Support\Factories;

use Tests\Support\Interfaces\DummyInterface;

class BadFactory implements DummyInterface
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
            'name'    => 'Bad Factory',
            'uid'     => 'bad',
            'summary' => 'This factory does not implement HandlerInterface as it should',
        ];
    }

    public function process()
    {
        return 'poop';
    }
}

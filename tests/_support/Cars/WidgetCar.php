<?php

namespace Tests\Support\Cars;

use Tests\Support\Interfaces\ExtendedInterface;

class WidgetCar implements ExtendedInterface
{
    /**
     * Returns this handler's identifier,
     * unique per path.
     */
    public static function handlerId(): string
    {
        return 'widget';
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
            'name'    => 'Widget Plant',
            'uid'     => 'widget',
            'summary' => "The world's largest supplier of widgets!",
            'cost'    => 10,
            'list'    => 'one,two,three,four',
        ];
    }

    public function process()
    {
        return 'widget';
    }
}

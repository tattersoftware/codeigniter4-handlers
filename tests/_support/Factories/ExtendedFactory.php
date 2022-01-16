<?php

namespace Tests\Support\Factories;

use Tests\Support\Interfaces\ExtendedInterface;

class ExtendedFactory extends CarFactory
{
    public const RETURN_TYPE = ExtendedInterface::class;

    public static function handlerId(): string
    {
        return 'extended';
    }

    public static function attributes(): array
    {
        return [
            'name' => 'Factory of Extended Cars',
        ];
    }
}

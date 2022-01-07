<?php

namespace Tests\Support\Factories;

use Tests\Support\Interfaces\ExtendedInterface;

class ExtendedFactory extends CarFactory
{
    public static function handlerId(): string
    {
        return 'extended';
    }

    public function getInterface(): string
    {
        return ExtendedInterface::class;
    }
}

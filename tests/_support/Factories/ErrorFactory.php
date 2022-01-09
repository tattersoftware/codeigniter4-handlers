<?php

namespace Tests\Support\Factories;

class ErrorFactory extends ExtendedFactory
{
    public static function handlerId(): string
    {
        return 'error';
    }

    /**
     * Returns an erroneous search path.
     */
    public function getPath(): string
    {
        return '';
    }
}

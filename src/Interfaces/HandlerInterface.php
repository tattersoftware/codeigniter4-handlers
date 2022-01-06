<?php

namespace Tatter\Handlers\Interfaces;

/**
 * Interface for anything discoverable by the Handlers Library.
 * Most paths will want their own extended interface.
 */
interface HandlerInterface
{
    /**
     * Returns this handler's identifier,
     * unique per path.
     */
    public static function handlerId(): string;

    /**
     * Returns the array of path-specific attributes.
     *
     * @return array<string, scalar>
     */
    public static function attributes(): array;
}

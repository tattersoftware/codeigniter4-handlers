<?php

namespace Tests\Support\Factories;

use BadMethodCallException;
use Tatter\Handlers\BaseFactory;
use Tests\Support\Cars\CarInterface;

/**
 * Error Factory Class
 *
 * Designed to throw an exception during discovery.
 */
class ErrorFactory extends BaseFactory
{
    public const HANDLER_ID   = 'error';
    public const HANDLER_PATH = 'Fruits';
    public const HANDLER_TYPE = CarInterface::class;

    /**
     * Returns the cache key for this Factory.
     */
    public static function cacheKey(): string
    {
        throw new BadMethodCallException('You should not be here!');
    }
}

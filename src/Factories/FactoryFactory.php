<?php

namespace Tatter\Handlers\Factories;

use Tatter\Handlers\BaseFactory;

/**
 * Factory Factory Class
 *
 * Used to discover other Factory classes
 * which are set up as handlers themselves.
 */
class FactoryFactory extends BaseFactory
{
    public const HANDLER_ID   = 'factory';
    public const HANDLER_PATH = 'Factories';
    public const HANDLER_TYPE = BaseFactory::class;
}

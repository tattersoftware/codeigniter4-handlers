<?php

namespace Tests\Support\Factories;

use Tatter\Handlers\BaseFactory;
use Tests\Support\Cars\CarInterface;

class CarFactory extends BaseFactory
{
    public const HANDLER_ID   = 'car';
    public const HANDLER_PATH = 'Cars';
    public const HANDLER_TYPE = CarInterface::class;
}

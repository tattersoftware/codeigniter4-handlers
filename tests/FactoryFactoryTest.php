<?php

use Tatter\Handlers\Factories\FactoryFactory;
use Tests\Support\Factories\CarFactory;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class FactoryFactoryTest extends TestCase
{
    public function testDiscovers()
    {
        // Discovery is alphabetical by ID
        $expected = [
            'car'     => CarFactory::class,
            'factory' => FactoryFactory::class,
        ];

        $factory = new FactoryFactory();
        $result  = $factory->findAll();

        $this->assertSame($expected, $result);
    }
}

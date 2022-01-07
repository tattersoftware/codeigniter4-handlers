<?php

use Tatter\Handlers\Factories\FactoryFactory;
use Tests\Support\Factories\CarFactory;
use Tests\Support\Factories\ExtendedFactory;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class FactoryFactoryTest extends TestCase
{
    public function testDiscovers()
    {
        // Discovery is alphabetical by handlerId
        $expected = [
            CarFactory::class,
            ExtendedFactory::class,
            FactoryFactory::class,
        ];

        $factory = new FactoryFactory();
        $result  = $factory->findAll();

        $this->assertSame($expected, $result);
    }
}

<?php

use Tests\Support\Factories\CarFactory;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class CacheTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        CarFactory::clearCache();
    }

    public function testDiscoveryUsesCache(): void
    {
        // Reenable caching
        config('Handlers')->cacheDuration = MINUTE;

        $expected = ['bam' => 'Foo\Bar\Baz'];
        cache()->save('handlers-cars', $expected);

        $result = CarFactory::findAll();

        $this->assertSame($expected, $result); // @phpstan-ignore-line
    }

    public function testDiscoveryCreatesCache(): void
    {
        // Reenable caching
        config('Handlers')->cacheDuration = MINUTE;

        CarFactory::findAll();

        $result = cache()->get('handlers-cars');

        $this->assertCount(2, $result);
        $this->assertSame('Tests\Support\Cars\PopCar', $result['pop']);
    }

    public function testDiscoveryIgnoresCache(): void
    {
        $expected = [
            'pop'    => 'Tests\Support\Cars\PopCar',
            'widget' => 'Tests\Support\Cars\WidgetCar',
        ];

        cache()->save('handlers-cars', [
            'pop' => 'Foo\Bar\Baz',
        ]);

        $result = CarFactory::findAll();

        $this->assertSame($expected, $result);
    }
}

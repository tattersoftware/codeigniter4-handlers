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

        $this->factory->clearCache();
    }

    public function testDiscoveryUsesCache()
    {
        // Reenable caching
        $config                = config('Handlers');
        $config->cacheDuration = MINUTE;

        $class = 'Foo\Bar\Baz';
        cache()->save('handlers-cars', [
            'bam' => ['id' => 'bam', 'class' => $class],
        ]);

        $this->factory = new CarFactory($config);
        $result        = $this->factory->first();

        $this->assertSame($class, $result); // @phpstan-ignore-line
    }

    public function testDiscoveryCreatesCache()
    {
        // Reenable caching
        $config                = config('Handlers');
        $config->cacheDuration = MINUTE;

        $this->factory = new CarFactory($config);

        $result = cache()->get('handlers-cars');

        $this->assertCount(2, $result);
        $this->assertSame('Tests\Support\Cars\PopCar', $result['pop']['class']);
    }

    public function testDiscoveryIgnoresCache()
    {
        $expected = 'Tests\Support\Cars\PopCar';

        cache()->save('handlers-cars', [
            'Foo\Bar\Baz' => ['name' => 'foobar'],
        ]);

        $result = $this->factory->first();

        $this->assertSame($expected, $result);
    }
}

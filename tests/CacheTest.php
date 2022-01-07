<?php

use Tests\Support\Managers\FactoryManager;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class CacheTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->manager->clearCache();
    }

    public function testDiscoveryUsesCache()
    {
        // Reenable caching
        $config                = config('Handlers');
        $config->cacheDuration = MINUTE;

        $class = 'Foo\Bar\Baz';
        cache()->save('handlers-factories', [
            'bam' => ['id' => 'bam', 'class' => $class],
        ]);

        $this->manager = new FactoryManager($config);
        $result        = $this->manager->first();

        $this->assertSame($class, $result); // @phpstan-ignore-line
    }

    public function testDiscoveryCreatesCache()
    {
        // Reenable caching
        $config                = config('Handlers');
        $config->cacheDuration = MINUTE;

        $this->manager = new FactoryManager($config);

        $result = cache()->get('handlers-factories');

        $this->assertCount(2, $result);
        $this->assertSame('Tests\Support\Factories\PopFactory', $result['pop']['class']);
    }

    public function testDiscoveryIgnoresCache()
    {
        $expected = 'Tests\Support\Factories\PopFactory';

        cache()->save('handlers-factories', [
            'Foo\Bar\Baz' => ['name' => 'foobar'],
        ]);

        $result = $this->manager->first();

        $this->assertSame($expected, $result);
    }
}

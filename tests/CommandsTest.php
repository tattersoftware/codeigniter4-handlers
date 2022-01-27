<?php

use CodeIgniter\Test\Filters\CITestStreamFilter;
use Tatter\Handlers\Factories\FactoryFactory;
use Tests\Support\Factories\CarFactory;
use Tests\Support\Factories\ErrorFactory;
use Tests\Support\TestCase;

/**
 * @see https://github.com/codeigniter4/CodeIgniter4/blob/develop/tests/system/Commands/HelpCommandTest.php
 *
 * @internal
 */
final class CommandsTest extends TestCase
{
    private $streamFilter;

    protected function setUp(): void
    {
        parent::setUp();

        CITestStreamFilter::$buffer = '';
        $this->streamFilter         = stream_filter_append(STDOUT, 'CITestStreamFilter');
        $this->streamFilter         = stream_filter_append(STDERR, 'CITestStreamFilter');

        // Make sure caching is enabled
        config('Handlers')->cacheDuration = MINUTE;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        stream_filter_remove($this->streamFilter);
    }

    protected function getBuffer()
    {
        return CITestStreamFilter::$buffer;
    }

    public function testCacheFailsCachingDisabled()
    {
        config('Handlers')->cacheDuration = null;

        command('handlers:cache');

        $this->assertStringContainsString('Handler caching is disabled by the Tatter\Handlers Config file', $this->getBuffer());
    }

    public function testCacheCreatesCache()
    {
        command('handlers:cache');

        $result = cache()->get('handlers-factories');
        $this->assertCount(2, $result);
        $this->assertSame(['car', 'factory'], array_keys($result));

        $result = cache()->get('handlers-cars');
        $this->assertCount(2, $result);
        $this->assertSame('Tests\Support\Cars\PopCar', $result['pop']);
    }

    public function testCacheReportsErrors()
    {
        // Stop ignoring the ErrorFactory
        unset(config('Handlers')->ignoredClasses[1]);

        command('handlers:cache');

        $this->assertStringContainsString('BadMethodCallException', $this->getBuffer());
        $this->assertStringContainsString('Total errors encountered: 1', $this->getBuffer());

        $result = cache()->get('handlers-factories');
        $this->assertCount(3, $result);
        $this->assertSame(ErrorFactory::class, $result['error']);
    }

    public function testCacheErrorsNoHandlers()
    {
        // Ignore all Factories
        config('Handlers')->ignoredClasses = [
            CarFactory::class,
            ErrorFactory::class,
            FactoryFactory::class,
        ];

        command('handlers:cache');

        $this->assertStringContainsString('No factories discovered!', $this->getBuffer());
    }

    public function testClearRemovesCache()
    {
        command('handlers:cache');
        $this->assertNotNull(cache()->get('handlers-factories'));

        command('handlers:clear');
        $this->assertNull(cache()->get('handlers-cars'));
        $this->assertNull(cache()->get('handlers-factories'));
    }
}

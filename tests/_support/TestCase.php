<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Handlers\BaseFactory;
use Tests\Support\Cars\CollisionCar;
use Tests\Support\Factories\ErrorFactory;

/**
 * @internal
 */
abstract class TestCase extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Disable caching for most tests
        $config                = config('Handlers');
        $config->cacheDuration = null;

        // Skip the erroneous handler until testing it specifically
        $config->ignoredClasses = [CollisionCar::class, ErrorFactory::class];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        BaseFactory::reset();
    }
}

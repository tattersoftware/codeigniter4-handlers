<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Factories\CarFactory;
use Tests\Support\Factories\ErrorFactory;

/**
 * @internal
 */
abstract class TestCase extends CIUnitTestCase
{
    /**
     * The configuration.
     */
    protected CarFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable caching for most tests
        $config                = config('Handlers');
        $config->cacheDuration = null;
        $this->factory         = new CarFactory($config);

        // Skip the erroneous handler until testing it specifically
        $config->ignoredClasses = [ErrorFactory::class];
    }
}

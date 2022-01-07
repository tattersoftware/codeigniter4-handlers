<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Factories\CarFactory;

/**
 * @internal
 */
abstract class TestCase extends CIUnitTestCase
{
    /**
     * The configuration.
     *
     * @var CarFactory
     */
    protected $factory;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable caching for most tests
        $config                = config('Handlers');
        $config->cacheDuration = null;
        $this->factory         = new CarFactory($config);
    }
}

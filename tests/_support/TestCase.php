<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Managers\FactoryManager;

/**
 * @internal
 */
abstract class TestCase extends CIUnitTestCase
{
    /**
     * The configuration.
     *
     * @var FactoryManager
     */
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable caching for most tests
        $config                = config('Handlers');
        $config->cacheDuration = null;
        $this->manager         = new FactoryManager($config);
    }
}

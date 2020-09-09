<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tatter\Handlers\Handlers;

class HandlerTestCase extends CIUnitTestCase
{
	/**
	 * The configuration.
	 *
	 * @var HandlersConfig
	 */
	protected $config;

	/**
	 * Instance of our Handlers library.
	 *
	 * @var Handlers
	 */
	protected $handlers;

	public function setUp(): void
	{
		parent::setUp();

		$this->config = new HandlersConfig();
		$this->config->cacheDuration = MINUTE;

		$this->handlers = new Handlers('Factories', $this->config);
	}

	public function tearDown(): void
	{
		parent::tearDown();

		$this->handlers->cacheClear();
	}
}

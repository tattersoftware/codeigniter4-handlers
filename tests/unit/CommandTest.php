<?php

use CodeIgniter\Config\Config;
use CodeIgniter\Test\Filters\CITestStreamFilter;
use Tests\Support\HandlerTestCase;

/**
 * @see https://github.com/codeigniter4/CodeIgniter4/blob/develop/tests/system/Commands/HelpCommandTest.php
 */
class CommandTest extends HandlerTestCase
{
	private $streamFilter;

	protected function setUp(): void
	{
		parent::setUp();

		CITestStreamFilter::$buffer = '';
		$this->streamFilter         = stream_filter_append(STDOUT, 'CITestStreamFilter');
		$this->streamFilter         = stream_filter_append(STDERR, 'CITestStreamFilter');
	}

	protected function tearDown(): void
	{
		stream_filter_remove($this->streamFilter);
	}

	protected function getBuffer()
	{
		return CITestStreamFilter::$buffer;
	}

	//--------------------------------------------------------------------

	public function testListCommandFails()
	{
		command('handlers:list');

		$this->assertStringContainsString('No paths are set for automatic discovery', $this->getBuffer());
	}

	public function testListCommandOutputsClasses()
	{
		$this->config->autoDiscover = ['Factories'];
		Config::injectMock('Handlers', $this->config);

		command('handlers:list');

		$this->assertStringContainsString('Tests\Support\Factories\WidgetFactory', $this->getBuffer());
	}
}

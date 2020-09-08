<?php

use Tests\Support\HandlerTestCase;

class LocatorTest extends HandlerTestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testFindConfig()
	{
		$configs = $this->library->findConfigs();
		
		$this->assertCount(1, $configs);
	}

	public function testFindHandlers()
	{
		$configClass = 'Tests\Support\Config\Handlers';
		
		$handlers = $this->library->findHandlers($configClass);
		
		$this->assertCount(1, $handlers);
		
		$class = reset($handlers);
		$this->assertEquals('Tests\Support\Factories\WidgetHandler', $class);
	}
}

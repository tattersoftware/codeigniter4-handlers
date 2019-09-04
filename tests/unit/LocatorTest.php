<?php

class LocatorTest extends CIModuleTests\Support\HandlerTestCase
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
		$configClass = 'CIModuleTests\Support\Config\Handlers';
		
		$handlers = $this->library->findHandlers($configClass);
		
		$this->assertCount(1, $handlers);
		
		$class = reset($handlers);
		$this->assertEquals('CIModuleTests\Support\Factories\WidgetHandler', $class);
	}
}

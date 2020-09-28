<?php

use Tatter\Handlers\Handlers;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tests\Support\HandlerTestCase;
use Tests\Support\Factories\WidgetFactory;

class LibraryTest extends HandlerTestCase
{
	public function testGetConfigReturnsConfig()
	{
		$result = $this->handlers->getConfig();

		$this->assertInstanceOf(HandlersConfig::class, $result);
		$this->assertEquals(MINUTE, $result->cacheDuration);
	}

	public function testGetPathReturnsPath()
	{
		$result = $this->handlers->getPath();

		$this->assertEquals('Factories', $result);
	}

	public function testSetPathChangesPath()
	{
		$path   = 'Daiquiris';
		$result = $this->handlers->setPath($path)->getPath();

		$this->assertEquals($path, $result);
	}

	public function testWhereCombinesFilters()
	{
		$this->handlers->where(['group' => 'East']);

		$result = $this->getPrivateProperty($this->handlers, 'filters');
		$this->assertEquals($result, [
			['group', '==', 'East', true],
		]);

		$this->handlers->where(['uid' => 'pop']);

		$result = $this->getPrivateProperty($this->handlers, 'filters');
		$this->assertEquals($result, [
			['group', '==', 'East', true],
			['uid', '==', 'pop', true],
		]);
	}

	public function testResetClearsFilters()
	{
		$this->handlers->where(['group' => 'East']);
		$this->handlers->reset();

		$result = $this->getPrivateProperty($this->handlers, 'filters');
		$this->assertEquals($result, []);
	}

	public function testGetHandlerClassReturnsClass()
	{
		$expected = 'Tests\Support\Factories\WidgetFactory';

		$file   = realpath(SUPPORTPATH . 'Factories/WidgetFactory.php');
		$result = $this->handlers->getHandlerClass($file, 'Tests\Support');

		$this->assertEquals($expected, $result);
	}

	public function testGetHandlerClassFails()
	{
		$file   = realpath(SUPPORTPATH . 'Factories/WidgetFactory.php');
		$result = $this->handlers->getHandlerClass($file, 'Foo\Bar');

		$this->assertNull($result);
	}

	public function testFilterHandlersRespectsLimit()
	{
		$limit = 1;

		$method = $this->getPrivateMethodInvoker($this->handlers, 'filterHandlers');
		$result = $method($limit);

		$this->assertCount($limit, $result);
	}

	public function testRegisterCallsHandlerRegister()
	{
		$this->handlers->register();

		$this->assertEquals(true, session('didRegister'));
	}

	public function testGetAttributesReturnsAttributes()
	{
		$result = $this->handlers->getAttributes(WidgetFactory::class);

		$this->assertIsArray($result);
		$this->assertEquals('Widget Plant', $result['name']);
	}

	public function testGetAttributesReturnsNull()
	{
		$result = $this->handlers->getAttributes('Imaginary\Handler\Class');

		$this->assertNull($result);
	}
}

<?php

use Tatter\Handlers\Handlers;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tests\Support\HandlerTestCase;

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

	public function testWhereMergesCriteria()
	{
		$this->handlers->where(['group' => 'East']);

		$result = $this->getPrivateProperty($this->handlers, 'criteria');
		$this->assertEquals($result, ['group' => 'East']);

		$this->handlers->where(['uid' => 'pop']);

		$result = $this->getPrivateProperty($this->handlers, 'criteria');
		$this->assertEquals($result, ['group' => 'East', 'uid' => 'pop']);
	}

	public function testResetClearsCriteria()
	{
		$this->handlers->where(['group' => 'East']);
		$this->handlers->reset();

		$result = $this->getPrivateProperty($this->handlers, 'criteria');
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

	public function testAllDiscoversAll()
	{
		$expected = [
			'Tests\Support\Factories\PopFactory',
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->all();

		$this->assertEquals($expected, $result);
	}

	public function testAllRespectsCriteria()
	{
		$expected = [
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->where(['uid' => 'widget'])->all();

		$this->assertEquals($expected, $result);
	}

	public function testAllResetsCriteria()
	{
		$this->handlers->where(['uid' => 'widget'])->all();

		$result = $this->getPrivateProperty($this->handlers, 'criteria');

		$this->assertEquals([], $result);
	}

	public function testFirstReturnsSingleton()
	{
		$expected = 'Tests\Support\Factories\PopFactory';

		$result = $this->handlers->first();

		$this->assertEquals($expected, $result);
	}

	public function testFirstRespectsCriteria()
	{
		$expected = [
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->where(['uid' => 'widget'])->all();

		$this->assertEquals($expected, $result);
	}

	public function testFirstResetsCriteria()
	{
		$this->handlers->where(['uid' => 'widget'])->first();

		$result = $this->getPrivateProperty($this->handlers, 'criteria');

		$this->assertEquals([], $result);
	}

	public function testRegisterCallsHandlerRegister()
	{
		$this->handlers->register();

		$this->assertEquals(true, session('didRegister'));
	}
}

<?php

use Tests\Support\HandlerTestCase;

class LibraryTest extends HandlerTestCase
{
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

	public function testWhereMergesFilters()
	{
		$this->handlers->where(['group' => 'East']);

		$result = $this->getPrivateProperty($this->handlers, 'filters');
		$this->assertEquals($result, ['group' => 'East']);

		$this->handlers->where(['uid' => 'pop']);

		$result = $this->getPrivateProperty($this->handlers, 'filters');
		$this->assertEquals($result, ['group' => 'East', 'uid' => 'pop']);
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

	public function testDiscoverHandlersRespectsLimit()
	{
		$limit = 1;

		$method = $this->getPrivateMethodInvoker($this->handlers, 'discoverHandlers');
		$method($limit);

		$result = $this->getPrivateProperty($this->handlers, 'discovered');

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

	public function testAllRespectsFilters()
	{
		$expected = [
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->where(['uid' => 'widget'])->all();

		$this->assertEquals($expected, $result);
	}

	public function testAllResetsFilters()
	{
		$this->handlers->where(['uid' => 'widget'])->all();

		$result = $this->getPrivateProperty($this->handlers, 'filters');

		$this->assertEquals([], $result);
	}

	public function testFirstReturnsOne()
	{
		$expected = 'Tests\Support\Factories\PopFactory';

		$result = $this->handlers->first();

		$this->assertEquals($expected, $result);
	}

	public function testFirstRespectsFilters()
	{
		$expected = [
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->where(['uid' => 'widget'])->all();

		$this->assertEquals($expected, $result);
	}

	public function testFirstResetsFilters()
	{
		$this->handlers->where(['uid' => 'widget'])->first();

		$result = $this->getPrivateProperty($this->handlers, 'filters');

		$this->assertEquals([], $result);
	}
}

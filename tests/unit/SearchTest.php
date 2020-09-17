<?php

use Tatter\Handlers\BaseHandler;
use Tatter\Handlers\Handlers;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tests\Support\HandlerTestCase;

class SearchTest extends HandlerTestCase
{
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

	//--------------------------------------------------------------------

	/**
	 * @dataProvider provideNames
	 */
	public function testNamedFindsMatch($name, $success)
	{
		$result = $this->handlers->named($name);

		$this->assertEquals($success, (bool) $result);
	}

	public function provideNames()
	{
		return [
			['',                                        false],
			[' ',                                       false],
			['pop',                                     true],
			['PopFactory',                              true],
			['Pop Factory',                             true],
			['Bad Factory',                             false],
			['Not A Factory',                           false],
			['Tests\\Support\\Factories',               false],
			['Tests\\Support\Factories\\PopFactory',    true],
			['\\Tests\\Support\\Factories\\PopFactory', true],
		];
	}
}

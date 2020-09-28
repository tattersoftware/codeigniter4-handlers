<?php

use Tatter\Handlers\BaseHandler;
use Tatter\Handlers\Handlers;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tests\Support\HandlerTestCase;

class SearchTest extends HandlerTestCase
{
	public function testWhereFilters()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
			->where(['uid' => 'widget'])
			->findAll();

		$this->assertEquals($expected, $result);
	}

	public function testWhereUsesOperators()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
			->where(['cost >' => 5])
			->findAll();

		$this->assertEquals($expected, $result);
	}

	public function testWhereSupportsCsv()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
			->where(['list has' => 'three'])
			->findAll();

		$this->assertEquals($expected, $result);
	}

	public function testWhereMissingAttribute()
	{
		$result = $this->handlers
			->where(['foo' => 'bar'])
			->findAll();

		$this->assertEquals([], $result);
	}

	public function testOrWhereIgnoresOtherFilters()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
			->where(['foo' => 'bar'])
			->orWhere(['uid' => 'widget'])
			->findAll();

		$this->assertEquals($expected, $result);
	}

	//--------------------------------------------------------------------

	public function testFindAllDiscoversAll()
	{
		$expected = [
			'Tests\Support\Factories\PopFactory',
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->findAll();

		$this->assertEquals($expected, $result);
	}

	public function testFindAllRespectsFilters()
	{
		$expected = [
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->where(['uid' => 'widget'])->findAll();

		$this->assertEquals($expected, $result);
	}

	public function testFindAllResetsFilters()
	{
		$this->handlers->where(['uid' => 'widget'])->findAll();

		$result = $this->getPrivateProperty($this->handlers, 'filters');

		$this->assertEquals([], $result);
	}

	public function testFirstReturnsSingleton()
	{
		$expected = 'Tests\Support\Factories\PopFactory';

		$result = $this->handlers->first();

		$this->assertEquals($expected, $result);
	}

	public function testFirstRespectsFilters()
	{
		$expected = 'Tests\Support\Factories\WidgetFactory';

		$result = $this->handlers->where(['uid' => 'widget'])->first();

		$this->assertEquals($expected, $result);
	}

	public function testFirstResetsFilters()
	{
		$this->handlers->where(['uid' => 'widget'])->first();

		$result = $this->getPrivateProperty($this->handlers, 'filters');

		$this->assertEquals([], $result);
	}

	//--------------------------------------------------------------------

	/**
	 * @dataProvider provideNames
	 */
	public function testFindFindsMatch($name, $success)
	{
		$result = $this->handlers->find($name);

		$this->assertEquals($success, (bool) $result);
	}

	public function provideNames()
	{
		return [
			[
				'',
				false,
			],
			[
				' ',
				false,
			],
			[
				'pop',
				true,
			],
			[
				'PopFactory',
				true,
			],
			[
				'Pop Factory',
				true,
			],
			[
				'Bad Factory',
				false,
			],
			[
				'Not A Factory',
				false,
			],
			[
				'Tests\\Support\\Factories',
				false,
			],
			[
				'Tests\\Support\Factories\\PopFactory',
				true,
			],
			[
				'\\Tests\\Support\\Factories\\PopFactory',
				true,
			],
		];
	}
}

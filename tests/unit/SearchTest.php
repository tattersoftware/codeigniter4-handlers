<?php

use Tests\Support\HandlerTestCase;

/**
 * @internal
 */
final class SearchTest extends HandlerTestCase
{
	public function testWhereFilters()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
		    ->where(['uid' => 'widget'])
		    ->findAll();

		$this->assertSame($expected, $result);
	}

	public function testWhereUsesOperators()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
		    ->where(['cost >' => 5])
		    ->findAll();

		$this->assertSame($expected, $result);
	}

	public function testWhereSupportsCsv()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
		    ->where(['list has' => 'three'])
		    ->findAll();

		$this->assertSame($expected, $result);
	}

	public function testWhereMissingAttribute()
	{
		$result = $this->handlers
		    ->where(['foo' => 'bar'])
		    ->findAll();

		$this->assertSame([], $result);
	}

	public function testOrWhereIgnoresOtherFilters()
	{
		$expected = ['Tests\Support\Factories\WidgetFactory'];

		$result = $this->handlers
		    ->where(['foo' => 'bar'])
		    ->orWhere(['uid' => 'widget'])
		    ->findAll();

		$this->assertSame($expected, $result);
	}

	//--------------------------------------------------------------------

	public function testFindAllDiscoversAll()
	{
		$expected = [
			'Tests\Support\Factories\PopFactory',
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->findAll();

		$this->assertSame($expected, $result);
	}

	public function testFindAllRespectsFilters()
	{
		$expected = [
			'Tests\Support\Factories\WidgetFactory',
		];

		$result = $this->handlers->where(['uid' => 'widget'])->findAll();

		$this->assertSame($expected, $result);
	}

	public function testFindAllResetsFilters()
	{
		$this->handlers->where(['uid' => 'widget'])->findAll();

		$result = $this->getPrivateProperty($this->handlers, 'filters');

		$this->assertSame([], $result);
	}

	public function testFirstReturnsSingleton()
	{
		$expected = 'Tests\Support\Factories\PopFactory';

		$result = $this->handlers->first();

		$this->assertSame($expected, $result);
	}

	public function testFirstRespectsFilters()
	{
		$expected = 'Tests\Support\Factories\WidgetFactory';

		$result = $this->handlers->where(['uid' => 'widget'])->first();

		$this->assertSame($expected, $result);
	}

	public function testFirstResetsFilters()
	{
		$this->handlers->where(['uid' => 'widget'])->first();

		$result = $this->getPrivateProperty($this->handlers, 'filters');

		$this->assertSame([], $result);
	}

	//--------------------------------------------------------------------

	/**
	 * @dataProvider provideNames
	 *
	 * @param mixed $name
	 * @param mixed $success
	 */
	public function testFindFindsMatch($name, $success)
	{
		$result = $this->handlers->find($name);

		$this->assertSame($success, (bool) $result);
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

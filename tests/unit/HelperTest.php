<?php

use Tatter\Handlers\Handlers;
use Tests\Support\HandlerTestCase;

class HelperTest extends HandlerTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		helper('handlers');
	}

	public function testHelperReturnsLibrary()
	{
		$result = handlers();

		$this->assertInstanceOf(Handlers::class, $result);
	}

	public function testHelperRetainsPath()
	{
		$path = 'Marmalade';
		handlers($path);

		$result = handlers()->getPath();

		$this->assertEquals($path, $result);
	}
}

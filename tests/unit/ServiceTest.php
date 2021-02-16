<?php

use Config\Services;
use Tests\Support\HandlerTestCase;

class ServiceTest extends HandlerTestCase
{
	public function testReturnsExpectedPath()
	{
		$foos = Services::handlers('foo');
		$bars = Services::handlers('bar');

		$this->assertEquals('foo', $foos->getPath());
		$this->assertEquals('bar', $bars->getPath());
	}
}

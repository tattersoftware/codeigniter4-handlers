<?php

use Config\Services;
use Tests\Support\HandlerTestCase;

/**
 * @internal
 */
final class ServiceTest extends HandlerTestCase
{
	public function testReturnsExpectedPath()
	{
		$foos = Services::handlers('foo');
		$bars = Services::handlers('bar');

		$this->assertSame('foo', $foos->getPath());
		$this->assertSame('bar', $bars->getPath());
	}
}

<?php

use Tatter\Handlers\BaseHandler;
use Tests\Support\HandlerTestCase;

class BaseHandlerTest extends HandlerTestCase
{
	public function testUsesDefaults()
	{
		$handler = new class extends BaseHandler {
			protected $defaults = [
				'foo' => 'bar',
			];
		};

		$this->assertEquals('bar', $handler->foo);
	}
}

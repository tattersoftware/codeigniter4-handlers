<?php

use Tatter\Handlers\Handlers;
use Tests\Support\HandlerTestCase;

class CacheTest extends HandlerTestCase
{
	public function testDiscoveryUsesCache()
	{
		$class = 'Foo\Bar\Baz';

		cache()->save('handlers-factories', [
				   $class => ['name' => 'foobar'],
			   ]);

		$result = $this->handlers->first();

		$this->assertEquals($class, $result);
	}

	public function testDiscoveryIgnoresCache()
	{
		$expected = 'Tests\Support\Factories\PopFactory';

		$this->config->cacheDuration = null;
		$handlers                    = new Handlers('Factories', $this->config);

		cache()->save('handlers-factories', [
				   'Foo\Bar\Baz' => ['name' => 'foobar'],
			   ]);

		$result = $this->handlers->first();

		$this->assertEquals($expected, $result);
	}
}

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
}

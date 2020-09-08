<?php

use Tests\Support\HandlerTestCase;

class ErrorsTest extends HandlerTestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testSilentReturnErrors()
	{
		$configClass = 'Tests\Support\Config\Handlers';
		$handlers = $this->library->findHandlers($configClass);
		
		$errors = $this->library->getErrors();

		$this->assertContains('Required properties missing from class: Tests\Support\Factories\BadHandler', $errors);
	}
}

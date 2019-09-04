<?php

class ErrorsTest extends CIModuleTests\Support\HandlerTestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testSilentReturnErrors()
	{
		$configClass = 'CIModuleTests\Support\Config\Handlers';
		$handlers = $this->library->findHandlers($configClass);
		
		$errors = $this->library->getErrors();

		$this->assertContains('Required properties missing from class: CIModuleTests\Support\Factories\BadHandler', $errors);
	}
}

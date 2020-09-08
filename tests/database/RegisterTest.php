<?php

use Tests\Support\Models\FactoryModel;

class RegisterTest extends Tests\Support\DatabaseTestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testRegisterHandler()
	{
		// Get an instance of the model
		$model = new FactoryModel();
		$handlerClass = '\Tests\Support\Factories\WidgetHandler';
		
		// Get the attributes from the handler itself
		$handler = new $handlerClass();
		$row = $handler->attributes;
		$row['class'] = $handlerClass;

		// Create a new registration
		$handlerId = $model->insert($row);
		
		$this->assertEquals(1, $handlerId);
	}
}

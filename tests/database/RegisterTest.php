<?php

use \CIModuleTests\Support\Models\FactoryModel;

class RegisterTest extends CIModuleTests\Support\DatabaseTestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testRegisterHandler()
	{
		// Get an instance of the model
		$model = new FactoryModel();
		$handlerClass = '\CIModuleTests\Support\Factories\WidgetHandler';
		
		// Get the attributes from the handler itself
		$handler = new $handlerClass();
		$row = $handler->attributes;
		$row['class'] = $handlerClass;

		// Create a new registration
		$handlerId = $model->insert($row);
		
		$this->assertEquals(1, $handlerId);
	}
}

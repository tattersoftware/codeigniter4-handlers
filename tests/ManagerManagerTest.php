<?php

use Tatter\Handlers\Managers\ManagerManager;
use Tests\Support\Managers\FactoryManager;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ManagerManagerTest extends TestCase
{
    public function testDiscovers()
    {
    	// Discovery is alphabetical by handlerId
    	$expected = [
    		FactoryManager::class,
    		ManagerManager::class,
    	];

        $manager = new ManagerManager();
		$result  = $manager->findAll();

		$this->assertSame($expected, $result);
    }
}

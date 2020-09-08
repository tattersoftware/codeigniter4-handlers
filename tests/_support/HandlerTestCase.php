<?php namespace Tests\Support;

use CodeIgniter\Test\CIDatabaseTestCase;

class HandlerTestCase extends CIDatabaseTestCase
{
	// Instance of our Handlers library
	protected $library;

    public function setUp(): void
    {
        parent::setUp();

        $this->library = service('handlers');
    }
}

<?php namespace CIModuleTests\Support;

use Tatter\Handlers\Config\Services;

class HandlerTestCase extends \CodeIgniter\Test\CIDatabaseTestCase
{
	// Instance of our Handlers library
	protected $library;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->library = Services::handlers();
    }
}

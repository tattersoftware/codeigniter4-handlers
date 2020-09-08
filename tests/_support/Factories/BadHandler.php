<?php namespace Tests\Support\Factories;

use CodeIgniter\Events\Events;
use Config\Services;
use Tatter\Handlers\Handlers\BaseHandler;
use Tatter\Handlers\Interfaces\HandlerInterface;

class BadHandler extends BaseHandler
{
	// Attributes for Tatter\Handlers
	public $attributes = [
		'name'       => 'Bad Factory',
		'uid'        => 'bad',
		'icon'       => 'fas fa-skull-crossbones',
		'summary'    => 'This factory does not extend HandlerInterface as it should',
	];

	public function process()
	{
	}
}

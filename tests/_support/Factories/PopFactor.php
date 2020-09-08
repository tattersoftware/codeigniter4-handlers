<?php namespace Tests\Support\Factories;

use Tatter\Handlers\Interfaces\HandlerInterface;

class PopFactory implements HandlerInterface
{
	use \Tatter\Handlers\Traits\HandlerTrait;

	// Attributes for Tatter\Handlers
	public $attributes = [
		'name'       => 'Pop Factory',
		'uid'        => 'pop',
		'icon'       => 'fas fa-can',
		'summary'    => 'Makes pop',
	];

	public function process()
	{
		return 'pop';
	}
}

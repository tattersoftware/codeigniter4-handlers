<?php namespace Tests\Support\Factories;

class BadFactory
{
	use \Tatter\Handlers\Traits\HandlerTrait;

	// Attributes for Tatter\Handlers
	public $attributes = [
		'name'       => 'Bad Factory',
		'uid'        => 'bad',
		'icon'       => 'fas fa-skull-crossbones',
		'summary'    => 'This factory does not implement HandlerInterface as it should',
	];

	public function process()
	{
		return 'poop';
	}
}

<?php namespace Tests\Support\Factories;

use Tatter\Handlers\BaseHandler;

class WidgetFactory extends BaseHandler
{
	// Attributes for Tatter\Handlers
	public $attributes = [
		'group'   => 'East',
		'name'    => 'Widget Plant',
		'uid'     => 'widget',
		'summary' => "The world's largest supplier of widgets!",
		'cost'    => 10,
		'list'    => 'one,two,three,four',
	];

	public function process()
	{
		return 'widget';
	}
}

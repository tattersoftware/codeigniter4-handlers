<?php namespace Tests\Support\Factories;

use CodeIgniter\Events\Events;
use Config\Services;
use Tatter\Handlers\Handlers\BaseHandler;
use Tatter\Handlers\Interfaces\HandlerInterface;

class WidgetHandler extends BaseHandler implements HandlerInterface
{
	// Attributes for Tatter\Handlers
	public $attributes = [
		'name'       => 'Widget Plant',
		'uid'        => 'widget',
		'icon'       => 'fas fa-puzzle-piece',
		'summary'    => "The world's largest supplier of widgets!",
	];

	public function process()
	{
	}
}

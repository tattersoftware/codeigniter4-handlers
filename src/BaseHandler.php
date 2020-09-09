<?php namespace Tatter\Handlers;

use Tatter\Handlers\Interfaces\HandlerInterface;

class BaseHandler implements HandlerInterface
{
	use \Tatter\Handlers\Traits\HandlerTrait;

	/**
	 * Attributes for Tatter\Handlers.
	 *
	 * @var array
	 */
	protected $attributes;
}

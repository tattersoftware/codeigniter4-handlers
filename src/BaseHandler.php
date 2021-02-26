<?php namespace Tatter\Handlers;

use Tatter\Handlers\Interfaces\HandlerInterface;

abstract class BaseHandler implements HandlerInterface
{
	use \Tatter\Handlers\Traits\HandlerTrait;

	/**
	 * Attributes for Tatter\Handlers.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Checks for and merges default attributes.
	 */
	public function __construct()
	{
		if (property_exists($this, 'defaults') && is_array($this->defaults))
		{
			$this->attributes = array_merge($this->defaults, $this->attributes);
		}
	}
}

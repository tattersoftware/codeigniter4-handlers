<?php namespace Tatter\Exports\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class ExportsException extends \RuntimeException implements ExceptionInterface
{
	public static function forLoadFail($file, $error)
	{
		return new static(lang('Handlers.loadFail', [$file, $error]));
	}
	
	public static function forMissingClass($file, $class)
	{
		return new static(lang('Handlers.missingClass', [$file, $class]));
	}
	
	public static function forInvalidFormat($file)
	{
		return new static(lang('Handlers.invalidFormat', [$file]));
	}
}
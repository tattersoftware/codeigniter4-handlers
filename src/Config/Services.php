<?php namespace Tatter\Handlers\Config;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Config\BaseService;
use Tatter\Handlers\Handlers;
use Tatter\Handlers\Config\Handlers as HandlersConfig;

class Services extends BaseService
{
	/**
	 * @param string              $path
	 * @param HandlersConfig|null $config
	 * @param CacheInterface|null $cache
	 * @param boolean             $getShared
	 */
	public static function handlers(string $path = '', HandlersConfig $config = null, CacheInterface $cache = null, bool $getShared = true)
	{
		if ($getShared)
		{
			return static::getSharedInstance('handlers', $path, $config, $cache);
		}

		return new Handlers($path, $config, $cache);
	}
}

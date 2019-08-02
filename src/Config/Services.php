<?php namespace Tatter\Handlers\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Database\ConnectionInterface;

class Services extends BaseService
{
    public static function handlers(BaseConfig $config = null, bool $getShared = true)
    {
		if ($getShared)
			return static::getSharedInstance('handlers', $config);

		// If no config was injected then load one
		$config = $config ?? config('Handlers');
		return new \Tatter\Handlers\Handlers($config);
	}
}

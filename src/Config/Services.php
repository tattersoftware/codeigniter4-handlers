<?php

namespace Tatter\Handlers\Config;

use CodeIgniter\Cache\CacheInterface;
use Config\Services as BaseServices;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tatter\Handlers\Handlers;

class Services extends BaseServices
{
    public static function handlers(string $path = '', ?HandlersConfig $config = null, ?CacheInterface $cache = null, bool $getShared = true)
    {
        if ($getShared) {
            $service = static::getSharedInstance('handlers', $path, $config, $cache);

            // Need to make sure the path is correct
            if ($path && $path !== $service->getPath()) {
                $service = (clone $service)->setPath($path);
            }

            return $service;
        }

        return new Handlers($path, $config, $cache);
    }
}

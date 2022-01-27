<?php

namespace Tatter\Handlers\Config;

use CodeIgniter\Config\BaseConfig;

class Handlers extends BaseConfig
{
    /**
     * Classes to ignore across all handlers.
     *
     * @var array<string>
     */
    public array $ignoredClasses = [];

    /**
     * Number of seconds to cache discovered handlers.
     * Null disables caching.
     */
    public ?int $cacheDuration = DAY;
}

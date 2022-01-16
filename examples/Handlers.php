<?php

namespace Config;

/*
*
* This file contains example values to alter default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Handlers.php
*	2. Change any values
*	3. Remove any lines to fallback to defaults
*
*/

use Tatter\Handlers\Config\Handlers as BaseHandlers;

class Handlers extends BaseHandlers
{
    /**
     * Classes to ignore across all handlers.
     *
     * @var array<string>
     */
    public array $ignoredClasses = [];

    /**
     * Number of seconds to cache discovered handlers.
     * Null disables caching
     */
    public ?int $cacheDuration = DAY;
}

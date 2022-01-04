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

class Handlers extends \Tatter\Handlers\Config\Handlers
{
    /**
     * Classes to ignore across all handlers.
     *
     * @var array<string>
     */
    public $ignoredClasses = [];

    /**
     * Paths to check during automatic discovery.
     *
     * @var array<string>
     */
    public $autoDiscover = [];

    /**
     * Number of seconds to cache discovered handlers.
     * Null disables caching
     *
     * @var int|null
     */
    public $cacheDuration = DAY;
}

<?php

namespace Tatter\Handlers\Managers;

use Tatter\Handlers\BaseManager;
use Tatter\Handlers\Interfaces\HandlerInterface;

/**
 * Manager Manager Class
 *
 * Used to discover other Manager classes
 * which are set up as handlers themselves.
 */
class ManagerManager extends BaseManager implements HandlerInterface
{
    public static function handlerId(): string
    {
        return 'managers';
    }

    public static function attributes(): array
    {
        return [
            'name' => 'Manager of Managers',
        ];
    }

    /**
     * Returns the search path.
     */
    public function getPath(): string
    {
    	return 'Managers';
    }
}

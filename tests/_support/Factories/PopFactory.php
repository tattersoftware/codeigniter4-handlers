<?php

namespace Tests\Support\Factories;

use Tatter\Handlers\Interfaces\HandlerInterface;

class PopFactory implements HandlerInterface
{
    use \Tatter\Handlers\Traits\HandlerTrait;

    // Attributes for Tatter\Handlers
    public $attributes = [
        'group'   => 'East',
        'name'    => 'Pop Factory',
        'uid'     => 'pop',
        'summary' => 'Makes pop',
        'cost'    => 1,
        'list'    => 'five,six',
    ];

    public function process()
    {
        return 'pop';
    }

    public function register()
    {
        session()->set(['didRegister' => true]);
    }
}

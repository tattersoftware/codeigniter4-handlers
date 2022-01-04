<?php

namespace Tests\Support\Factories;

class BadFactory
{
    use \Tatter\Handlers\Traits\HandlerTrait;

    // Attributes for Tatter\Handlers
    public $attributes = [
        'group'   => 'South',
        'name'    => 'Bad Factory',
        'uid'     => 'bad',
        'summary' => 'This factory does not implement HandlerInterface as it should',
    ];

    public function process()
    {
        return 'poop';
    }
}

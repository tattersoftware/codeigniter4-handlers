<?php

use Tatter\Handlers\BaseHandler;
use Tests\Support\HandlerTestCase;

/**
 * @internal
 */
final class BaseHandlerTest extends HandlerTestCase
{
    public function testUsesDefaults()
    {
        $handler                = new class () extends BaseHandler {
            protected $defaults = [
                'foo' => 'bar',
            ];
        };

        $this->assertSame('bar', $handler->foo);
    }

    public function testIsset()
    {
        $handler                  = new class () extends BaseHandler {
            protected $attributes = [
                'fruit' => 'banana',
            ];
        };

        $this->assertTrue(isset($handler->fruit));
        $this->assertFalse(isset($handler->meat));
    }
}

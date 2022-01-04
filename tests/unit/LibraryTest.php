<?php

use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tests\Support\Factories\WidgetFactory;
use Tests\Support\HandlerTestCase;

/**
 * @internal
 */
final class LibraryTest extends HandlerTestCase
{
    public function testGetConfigReturnsConfig()
    {
        $result = $this->handlers->getConfig();

        $this->assertInstanceOf(HandlersConfig::class, $result);
        $this->assertSame(MINUTE, $result->cacheDuration);
    }

    public function testGetPathReturnsPath()
    {
        $result = $this->handlers->getPath();

        $this->assertSame('Factories', $result);
    }

    public function testSetPathChangesPath()
    {
        $path   = 'Daiquiris';
        $result = $this->handlers->setPath($path)->getPath();

        $this->assertSame($path, $result);
    }

    public function testWhereCombinesFilters()
    {
        $this->handlers->where(['group' => 'East']);

        $result = $this->getPrivateProperty($this->handlers, 'filters');
        $this->assertSame($result, [
            ['group', '==', 'East', true],
        ]);

        $this->handlers->where(['uid' => 'pop']);

        $result = $this->getPrivateProperty($this->handlers, 'filters');
        $this->assertSame($result, [
            ['group', '==', 'East', true],
            ['uid', '==', 'pop', true],
        ]);
    }

    public function testResetClearsFilters()
    {
        $this->handlers->where(['group' => 'East']);
        $this->handlers->reset();

        $result = $this->getPrivateProperty($this->handlers, 'filters');
        $this->assertSame($result, []);
    }

    public function testGetHandlerClassReturnsClass()
    {
        $expected = 'Tests\Support\Factories\WidgetFactory';

        $file   = realpath(SUPPORTPATH . 'Factories/WidgetFactory.php');
        $result = $this->handlers->getHandlerClass($file, 'Tests\Support');

        $this->assertSame($expected, $result);
    }

    public function testGetHandlerClassFails()
    {
        $file   = realpath(SUPPORTPATH . 'Factories/WidgetFactory.php');
        $result = $this->handlers->getHandlerClass($file, 'Foo\Bar');

        $this->assertNull($result);
    }

    public function testFilterHandlersRespectsLimit()
    {
        $limit = 1;

        $method = $this->getPrivateMethodInvoker($this->handlers, 'filterHandlers');
        $result = $method($limit);

        $this->assertCount($limit, $result);
    }

    public function testRegisterCallsHandlerRegister()
    {
        $this->handlers->register();

        $this->assertTrue(session('didRegister'));
    }

    public function testGetAttributesReturnsAttributes()
    {
        $result = $this->handlers->getAttributes(WidgetFactory::class);

        $this->assertIsArray($result);
        $this->assertSame('Widget Plant', $result['name']);
    }

    public function testGetAttributesReturnsNull()
    {
        $result = $this->handlers->getAttributes('Imaginary\Handler\Class');

        $this->assertNull($result);
    }
}

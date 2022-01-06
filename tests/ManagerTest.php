<?php

use Tatter\Handlers\BaseManager;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tests\Support\FactoryManager;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class ManagerTest extends TestCase
{
    public function testInvalidPathThrows()
    {
        $this->expectException('UnexpectedValueException');
        $this->expectExceptionMessage('Invalid path provided: ');

        new class () extends BaseManager {
            public function getPath(): string
            {
                return '';
            }
        };
    }

    public function testNoDiscoveryReturnsEmptyArray()
    {
        $manager = new class () extends BaseManager {
            public function getPath(): string
            {
                return 'Bananas';
            }
        };

        $this->assertSame([], $manager->findAll());
    }

    public function testGetConfigReturnsConfig()
    {
        $result = $this->manager->getConfig();

        $this->assertInstanceOf(HandlersConfig::class, $result);
        $this->assertNull($result->cacheDuration);
    }

    public function testGetPathReturnsPath()
    {
        $result = $this->manager->getPath();

        $this->assertSame('Factories', $result);
    }

    public function testWhereCombinesFilters()
    {
        $this->manager->where(['group' => 'East']);

        $result = $this->getPrivateProperty($this->manager, 'filters');
        $this->assertSame($result, [
            ['group', '==', 'East', true],
        ]);

        $this->manager->where(['uid' => 'pop']);

        $result = $this->getPrivateProperty($this->manager, 'filters');
        $this->assertSame($result, [
            ['group', '==', 'East', true],
            ['uid', '==', 'pop', true],
        ]);
    }

    public function testResetClearsFilters()
    {
        $this->manager->where(['group' => 'East']);
        $this->manager->reset();

        $result = $this->getPrivateProperty($this->manager, 'filters');
        $this->assertSame($result, []);
    }

    public function testGetHandlerClassReturnsClass()
    {
        $expected = 'Tests\Support\Factories\WidgetFactory';

        $file   = realpath(SUPPORTPATH . 'Factories/WidgetFactory.php');
        $result = $this->manager->getHandlerClass($file, 'Tests\Support');

        $this->assertSame($expected, $result);
    }

    public function testGetHandlerClassFails()
    {
        $file   = realpath(SUPPORTPATH . 'Factories/WidgetFactory.php');
        $result = $this->manager->getHandlerClass($file, 'Foo\Bar');

        $this->assertNull($result);
    }

    public function testFilterHandlersRespectsLimit()
    {
        $limit = 1;

        $method = $this->getPrivateMethodInvoker($this->manager, 'filterHandlers');
        $result = $method($limit);

        $this->assertCount($limit, $result);
    }

    public function testGetAttributesReturnsAttributes()
    {
        $result = $this->manager->getAttributes('widget');

        $this->assertIsArray($result);
        $this->assertSame('Widget Plant', $result['name']);
    }

    public function testGetAttributesReturnsNull()
    {
        $result = $this->manager->getAttributes('Imaginary\Handler\Class');

        $this->assertNull($result);
    }

    public function testIgnoresClass()
    {
        $expected = ['Tests\Support\Factories\WidgetFactory'];

        $config                 = config('Handlers');
        $config->ignoredClasses = ['Tests\Support\Factories\PopFactory'];
        $this->manager          = new FactoryManager($config);

        $result = $this->manager->findAll();

        $this->assertSame($expected, $result);
    }

    public function testGetHandlerClassRequiresPhpExtension()
    {
        $result = $this->manager->getHandlerClass('foo', 'bar');

        $this->assertNull($result);
    }

    public function testGetHandlerClassRequiresInterface()
    {
        $result = $this->manager->getHandlerClass(SUPPORTPATH . 'Factories/BadFactory.php', 'Tests\Support');

        $this->assertNull($result);
    }
}

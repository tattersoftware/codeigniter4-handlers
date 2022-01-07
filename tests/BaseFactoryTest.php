<?php

use Tatter\Handlers\BaseFactory;
use Tatter\Handlers\Config\Handlers as HandlersConfig;
use Tests\Support\Factories\CarFactory;
use Tests\Support\Factories\ExtendedFactory;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class BaseFactoryTest extends TestCase
{
    public function testInvalidPathThrows()
    {
        $this->expectException('UnexpectedValueException');
        $this->expectExceptionMessage('Invalid path provided: ');

        new class () extends BaseFactory {
            public function getPath(): string
            {
                return '';
            }
        };
    }

    public function testNoDiscoveryReturnsEmptyArray()
    {
        $factory = new class () extends BaseFactory {
            public function getPath(): string
            {
                return 'Bananas';
            }
        };

        $this->assertSame([], $factory->findAll());
    }

    public function testGetConfigReturnsConfig()
    {
        $result = $this->factory->getConfig();

        $this->assertInstanceOf(HandlersConfig::class, $result);
        $this->assertNull($result->cacheDuration);
    }

    public function testGetPathReturnsPath()
    {
        $result = $this->factory->getPath();

        $this->assertSame('Cars', $result);
    }

    public function testWhereCombinesFilters()
    {
        $this->factory->where(['group' => 'East']);

        $result = $this->getPrivateProperty($this->factory, 'filters');
        $this->assertSame($result, [
            ['group', '==', 'East', true],
        ]);

        $this->factory->where(['uid' => 'pop']);

        $result = $this->getPrivateProperty($this->factory, 'filters');
        $this->assertSame($result, [
            ['group', '==', 'East', true],
            ['uid', '==', 'pop', true],
        ]);
    }

    public function testResetClearsFilters()
    {
        $this->factory->where(['group' => 'East']);
        $this->factory->reset();

        $result = $this->getPrivateProperty($this->factory, 'filters');
        $this->assertSame($result, []);
    }

    public function testGetHandlerClassReturnsClass()
    {
        $expected = 'Tests\Support\Cars\WidgetCar';

        $file   = realpath(SUPPORTPATH . 'Cars/WidgetCar.php');
        $result = $this->factory->getHandlerClass($file, 'Tests\Support');

        $this->assertSame($expected, $result);
    }

    public function testGetHandlerClassUsesExtendedInterface()
    {
        $factory = new ExtendedFactory();

        $result = $factory->getHandlerClass(SUPPORTPATH . 'Cars/WidgetCar.php', 'Tests\Support');
        $this->assertSame('Tests\Support\Cars\WidgetCar', $result);

        $result = $factory->getHandlerClass(SUPPORTPATH . 'Cars/PopCar.php', 'Tests\Support');
        $this->assertNull($result);
    }

    public function testGetHandlerClassRequiresPhpExtension()
    {
        $result = $this->factory->getHandlerClass('foo', 'bar');

        $this->assertNull($result);
    }

    public function testGetHandlerClassRequiresInterfaces()
    {
        $result = $this->factory->getHandlerClass(SUPPORTPATH . 'Cars/NotCar.php', 'Tests\Support');

        $this->assertNull($result);
    }

    public function testGetHandlerClassRequiresHandlerInterface()
    {
        $result = $this->factory->getHandlerClass(SUPPORTPATH . 'Cars/BadCar.php', 'Tests\Support');

        $this->assertNull($result);
    }

    public function testGetHandlerClassFails()
    {
        $file   = realpath(SUPPORTPATH . 'Cars/WidgetCar.php');
        $result = $this->factory->getHandlerClass($file, 'Foo\Bar');

        $this->assertNull($result);
    }

    public function testFilterHandlersRespectsLimit()
    {
        $limit = 1;

        $method = $this->getPrivateMethodInvoker($this->factory, 'filterHandlers');
        $result = $method($limit);

        $this->assertCount($limit, $result);
    }

    public function testGetAttributesReturnsAttributes()
    {
        $result = $this->factory->getAttributes('widget');

        $this->assertIsArray($result);
        $this->assertSame('Widget Plant', $result['name']);
    }

    public function testGetAttributesReturnsNull()
    {
        $result = $this->factory->getAttributes('Imaginary\Handler\Class');

        $this->assertNull($result);
    }

    public function testIgnoresClass()
    {
        $expected = ['Tests\Support\Cars\WidgetCar'];

        $config                 = config('Handlers');
        $config->ignoredClasses = ['Tests\Support\Cars\PopCar'];
        $this->factory          = new CarFactory($config);

        $result = $this->factory->findAll();

        $this->assertSame($expected, $result);
    }
}

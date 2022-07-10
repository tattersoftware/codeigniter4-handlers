<?php

use Tatter\Handlers\BaseFactory;
use Tatter\Handlers\Factories\FactoryFactory;
use Tests\Support\Factories\CarFactory;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class BaseFactoryTest extends TestCase
{
    public function testNoDiscoveryReturnsEmptyArray(): void
    {
        $factory                      = new class () extends BaseFactory {
            public const HANDLER_PATH = 'Bananas';
        };

        $this->assertSame([], $factory::findAll());
    }

    public function testGetHandlerClassReturnsClass(): void
    {
        $expected = 'Tests\Support\Cars\WidgetCar';

        $file   = realpath(SUPPORTPATH . 'Cars/WidgetCar.php');
        $result = CarFactory::getHandlerClass($file, 'Tests\Support');

        $this->assertSame($expected, $result);
    }

    public function testGetHandlerClassRequiresPhpExtension(): void
    {
        $result = CarFactory::getHandlerClass('foo', 'bar');

        $this->assertNull($result);
    }

    public function testGetHandlerClassRequiresInterfaces(): void
    {
        $result = CarFactory::getHandlerClass(SUPPORTPATH . 'Cars/NotCar.php', 'Tests\Support');

        $this->assertNull($result);
    }

    public function testGetHandlerClassRequiresHandlerInterface(): void
    {
        $result = CarFactory::getHandlerClass(SUPPORTPATH . 'Cars/BadCar.php', 'Tests\Support');

        $this->assertNull($result);
    }

    public function testGetHandlerClassFails(): void
    {
        $file   = realpath(SUPPORTPATH . 'Cars/WidgetCar.php');
        $result = CarFactory::getHandlerClass($file, 'Foo\Bar');

        $this->assertNull($result);
    }

    public function testIgnoresClass(): void
    {
        config('Handlers')->ignoredClasses[] = 'Tests\Support\Cars\PopCar';

        $expected = ['widget' => 'Tests\Support\Cars\WidgetCar'];
        $result   = CarFactory::findAll();

        $this->assertSame($expected, $result);
    }

    public function testCollision(): void
    {
        // Stop ignoring the collision clas
        config('Handlers')->ignoredClasses = [];

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Handlers have conflicting ID "pop": Tests\Support\Cars\CollisionCar, Tests\Support\Cars\PopCar');

        CarFactory::findAll();
    }

    public function testFindAll(): void
    {
        $expected = [
            'pop'    => 'Tests\Support\Cars\PopCar',
            'widget' => 'Tests\Support\Cars\WidgetCar',
        ];

        $result = CarFactory::findAll();

        $this->assertSame($expected, $result);
    }

    public function testFind(): void
    {
        $expected = 'Tests\Support\Cars\PopCar';

        $result = CarFactory::find('pop');

        $this->assertSame($expected, $result);
    }

    public function testFindThrows(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Unknown handler "banana" for ' . CarFactory::class);

        CarFactory::find('banana');
    }

    public function testResetSingle(): void
    {
        CarFactory::findAll();
        FactoryFactory::findAll();

        $result = $this->getPrivateProperty(BaseFactory::class, 'discovered');
        $this->assertSame(['Cars', 'Factories'], array_keys($result));

        CarFactory::reset();
        $result = $this->getPrivateProperty(BaseFactory::class, 'discovered');
        $this->assertSame(['Factories'], array_keys($result));
    }

    public function testResetAll(): void
    {
        CarFactory::findAll();
        FactoryFactory::findAll();

        $result = $this->getPrivateProperty(BaseFactory::class, 'discovered');
        $this->assertSame(['Cars', 'Factories'], array_keys($result));

        BaseFactory::reset();
        $result = $this->getPrivateProperty(BaseFactory::class, 'discovered');
        $this->assertSame([], array_keys($result));
    }
}

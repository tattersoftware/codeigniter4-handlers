<?php

use Tests\Support\TestCase;

/**
 * @internal
 */
final class SearchTest extends TestCase
{
    public function testInvalidOperatorThrows()
    {
        $this->expectException('UnexpectedValueException');
        $this->expectExceptionMessage('# is not a valid criteria operator');

        $this->factory
            ->where(['cost #' => 42])
            ->findAll();
    }

    public function testWhereFilters()
    {
        $expected = ['Tests\Support\Cars\WidgetCar'];

        $result = $this->factory
            ->where(['uid' => 'widget'])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testWhereUsesOperators()
    {
        $expected = ['Tests\Support\Cars\WidgetCar'];

        $result = $this->factory
            ->where(['cost >' => 5])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testWhereSupportsCsv()
    {
        $expected = ['Tests\Support\Cars\WidgetCar'];

        $result = $this->factory
            ->where(['list has' => 'three'])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testWhereMissingAttribute()
    {
        $result = $this->factory
            ->where(['foo' => 'bar'])
            ->findAll();

        $this->assertSame([], $result);
    }

    public function testOrWhereIgnoresOtherFilters()
    {
        $expected = ['Tests\Support\Cars\WidgetCar'];

        $result = $this->factory
            ->where(['foo' => 'bar'])
            ->orWhere(['uid' => 'widget'])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testFilterHandlersStopsAtLimit()
    {
        $expected = ['Tests\Support\Cars\PopCar'];

        $this->factory->where(['cost >=' => 1]);
        $method = $this->getPrivateMethodInvoker($this->factory, 'filterHandlers');
        $result = $method(1);

        $this->assertSame($expected, $result);
    }

    //--------------------------------------------------------------------

    public function testFindAllDiscoversAll()
    {
        $expected = [
            'Tests\Support\Cars\PopCar',
            'Tests\Support\Cars\WidgetCar',
        ];

        $result = $this->factory->findAll();

        $this->assertSame($expected, $result);
    }

    public function testFindAllRespectsFilters()
    {
        $expected = [
            'Tests\Support\Cars\WidgetCar',
        ];

        $result = $this->factory->where(['uid' => 'widget'])->findAll();

        $this->assertSame($expected, $result);
    }

    public function testFindAllResetsFilters()
    {
        $this->factory->where(['uid' => 'widget'])->findAll();

        $result = $this->getPrivateProperty($this->factory, 'filters');

        $this->assertSame([], $result);
    }

    public function testFirstReturnsSingleton()
    {
        $expected = 'Tests\Support\Cars\PopCar';

        $result = $this->factory->first();

        $this->assertSame($expected, $result);
    }

    public function testFirstRespectsFilters()
    {
        $expected = 'Tests\Support\Cars\WidgetCar';

        $result = $this->factory->where(['uid' => 'widget'])->first();

        $this->assertSame($expected, $result);
    }

    public function testFirstResetsFilters()
    {
        $this->factory->where(['uid' => 'widget'])->first();

        $result = $this->getPrivateProperty($this->factory, 'filters');

        $this->assertSame([], $result);
    }

    public function testFind()
    {
        $expected = 'Tests\Support\Cars\PopCar';

        $result = $this->factory->find('pop');

        $this->assertSame($expected, $result);
    }

    //--------------------------------------------------------------------

    /**
     * @dataProvider operatorProvider
     *
     * @param mixed $operator
     * @param mixed $input
     * @param mixed $expected
     */
    public function testOperators($operator, $input, $expected)
    {
        $criterium = 'cost ' . $operator;

        $result = $this->factory->where([$criterium => $input])->first();

        $this->assertSame($expected, $result);
    }

    public function operatorProvider()
    {
        return [
            ['==', '1', 'Tests\Support\Cars\PopCar'],
            ['==', 1, 'Tests\Support\Cars\PopCar'],
            ['=', 1, 'Tests\Support\Cars\PopCar'],
            ['=', true, 'Tests\Support\Cars\PopCar'],
            ['===', 1, 'Tests\Support\Cars\PopCar'],
            ['>', 1, 'Tests\Support\Cars\WidgetCar'],
            ['>=', 1, 'Tests\Support\Cars\PopCar'],
            ['<', 10, 'Tests\Support\Cars\PopCar'],
            ['<=', 10, 'Tests\Support\Cars\PopCar'],
        ];
    }
}

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

        $this->manager
            ->where(['cost #' => 42])
            ->findAll();
    }

    public function testWhereFilters()
    {
        $expected = ['Tests\Support\Factories\WidgetFactory'];

        $result = $this->manager
            ->where(['uid' => 'widget'])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testWhereUsesOperators()
    {
        $expected = ['Tests\Support\Factories\WidgetFactory'];

        $result = $this->manager
            ->where(['cost >' => 5])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testWhereSupportsCsv()
    {
        $expected = ['Tests\Support\Factories\WidgetFactory'];

        $result = $this->manager
            ->where(['list has' => 'three'])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testWhereMissingAttribute()
    {
        $result = $this->manager
            ->where(['foo' => 'bar'])
            ->findAll();

        $this->assertSame([], $result);
    }

    public function testOrWhereIgnoresOtherFilters()
    {
        $expected = ['Tests\Support\Factories\WidgetFactory'];

        $result = $this->manager
            ->where(['foo' => 'bar'])
            ->orWhere(['uid' => 'widget'])
            ->findAll();

        $this->assertSame($expected, $result);
    }

    public function testFilterHandlersStopsAtLimit()
    {
        $expected = ['Tests\Support\Factories\PopFactory'];

        $this->manager->where(['cost >=' => 1]);
		$method = $this->getPrivateMethodInvoker($this->manager, 'filterHandlers');
		$result = $method(1);

        $this->assertSame($expected, $result);
    }

    //--------------------------------------------------------------------

    public function testFindAllDiscoversAll()
    {
        $expected = [
            'Tests\Support\Factories\PopFactory',
            'Tests\Support\Factories\WidgetFactory',
        ];

        $result = $this->manager->findAll();

        $this->assertSame($expected, $result);
    }

    public function testFindAllRespectsFilters()
    {
        $expected = [
            'Tests\Support\Factories\WidgetFactory',
        ];

        $result = $this->manager->where(['uid' => 'widget'])->findAll();

        $this->assertSame($expected, $result);
    }

    public function testFindAllResetsFilters()
    {
        $this->manager->where(['uid' => 'widget'])->findAll();

        $result = $this->getPrivateProperty($this->manager, 'filters');

        $this->assertSame([], $result);
    }

    public function testFirstReturnsSingleton()
    {
        $expected = 'Tests\Support\Factories\PopFactory';

        $result = $this->manager->first();

        $this->assertSame($expected, $result);
    }

    public function testFirstRespectsFilters()
    {
        $expected = 'Tests\Support\Factories\WidgetFactory';

        $result = $this->manager->where(['uid' => 'widget'])->first();

        $this->assertSame($expected, $result);
    }

    public function testFirstResetsFilters()
    {
        $this->manager->where(['uid' => 'widget'])->first();

        $result = $this->getPrivateProperty($this->manager, 'filters');

        $this->assertSame([], $result);
    }

    public function testFind()
    {
        $expected = 'Tests\Support\Factories\PopFactory';

        $result = $this->manager->find('pop');

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

        $result = $this->manager->where([$criterium => $input])->first();

        $this->assertSame($expected, $result);
    }

    public function operatorProvider()
    {
        return [
            ['==', '1', 'Tests\Support\Factories\PopFactory'],
            ['==', 1, 'Tests\Support\Factories\PopFactory'],
            ['=', 1, 'Tests\Support\Factories\PopFactory'],
            ['=', true, 'Tests\Support\Factories\PopFactory'],
            ['===', 1, 'Tests\Support\Factories\PopFactory'],
            ['>', 1, 'Tests\Support\Factories\WidgetFactory'],
            ['>=', 1, 'Tests\Support\Factories\PopFactory'],
            ['<', 10, 'Tests\Support\Factories\PopFactory'],
            ['<=', 10, 'Tests\Support\Factories\PopFactory'],
        ];
    }
}

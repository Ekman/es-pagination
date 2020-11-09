<?php


use Nekman\EsPagination\EsUtility;
use PHPUnit\Framework\TestCase;

class EsUtilityTest extends TestCase
{
    /** @dataProvider provideCountHits */
    public function testCountHits($hits, $count)
    {
        $this->assertCount($count, EsUtility::hits($hits));
    }

    public function provideCountHits()
    {
        return [
            [
                ["hits" => ["hits" => [1, 2, 3, 4]]],
                4
            ]
        ];
    }

    /** @dataProvider provideHits */
    public function testHits($hits, $expected)
    {
        $this->assertEquals($expected, EsUtility::hits($hits));
    }

    public function provideHits()
    {
        return [
            [
                ["hits" => ["hits" => [1, 2, 3, 4]]],
                [1, 2, 3, 4]
            ]
        ];
    }
}

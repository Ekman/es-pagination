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

    /** @dataProvider provideResponse */
    public function testResponse($hits, $expected)
    {
        $this->assertEquals($expected, EsUtility::response($hits));
    }

    public function provideResponse()
    {
        return [
            "empty array" => [
                [],
                ["hits" => ["hits" => []]],
            ],
            "with hits" => [
                [1, 2, 3],
                ["hits" => ["hits" => [1, 2, 3]]],
            ],
        ];
    }

    /** @dataProvider provideParamsSort */
    public function testParamsSort($params, $sort, $expected)
    {
        $this->assertEquals($expected, EsUtility::paramsSort($params, $sort));
    }

    public function provideParamsSort()
    {
        return [
            "empty array" => [
                [],
                [["id" => "asc"]],
                [
                    "body" => [
                        "sort" => [["id" => "asc"]]
                    ]
                ],
            ],
            "existing sort" => [
                [
                    "body" => [
                        "sort" => [["name" => "desc"]]
                    ]
                ],
                [["id" => "asc"]],
                [
                    "body" => [
                        "sort" => [["id" => "asc"]]
                    ]
                ],
            ]
        ];
    }

    /** @dataProvider provideHitsId */
    public function testHitsId($hits, $expected)
    {
        $this->assertEquals($expected, iterator_to_array(EsUtility::hitsId($hits)));
    }

    public function provideHitsId()
    {
        return [
            [
                [
                    ["_id" => "foo"],
                    ["_id" => "bar"],
                    ["_id" => "hello"],
                    ["_id" => "world"],
                ],
                ["foo", "bar", "hello", "world"]
            ]
        ];
    }

    /** @dataProvider provideHit */
    public function testHit($id, $sort, $expected)
    {
        $this->assertEquals($expected, EsUtility::hit($id, $sort));
    }

    public function provideHit()
    {
        return [
            [
                "foo",
                [["bar" => "desc"]],
                ["_id" => "foo", "sort" => [["bar" => "desc"]]]
            ]
        ];
    }
}

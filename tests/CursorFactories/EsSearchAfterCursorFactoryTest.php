<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;
use PHPUnit\Framework\TestCase;

class EsSearchAfterCursorFactoryTest extends TestCase
{
    public function testResponses()
    {
		$this->markTestSkipped("Elasticsearch made " . Client::class . " final which makes testing unreasonably hard. Skipping until the bug is fixed.");

        $size = 3;

        $client = $this->createMock(Client::class);

        $client->expects($this->exactly(3))
            ->method("search")
            ->withConsecutive(
                [$this->equalTo([
                    "size" => $size,
                    "body" => [
                        "sort" => [["_id" => "asc"]],
                    ],
                ])],
                [$this->equalTo([
                    "size" => $size,
                    "body" => [
                        "sort" => [["_id" => "asc"]],
                        "search_after" => [3],
                    ],
                ])],
                [$this->equalTo([
                    "size" => $size,
                    "body" => [
                        "sort" => [["_id" => "asc"]],
                        "search_after" => [6],
                    ],
                ])],
            )
            ->willReturn(
                EsUtility::response([
                    EsUtility::hit(1, [1]),
                    EsUtility::hit(2, [2]),
                    EsUtility::hit(3, [3]),
                ]),
                EsUtility::response([
                    EsUtility::hit(4, [4]),
                    EsUtility::hit(5, [5]),
                    EsUtility::hit(6, [6]),
                ]),
                EsUtility::response()
            );

        $cursorFactory = new EsSearchAfterCursorFactory($client, $size);

        $hits = EsUtility::hitsId($cursorFactory->hits());
        $hits = iterator_to_array($hits);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $hits);
    }
}

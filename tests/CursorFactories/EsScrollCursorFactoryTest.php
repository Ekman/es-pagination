<?php

namespace Nekman\EsPagination\CursorFactories;

use Elastic\Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;
use PHPUnit\Framework\TestCase;

class EsScrollCursorFactoryTest extends TestCase
{
    public function testResponses()
    {
        $size = 3;
        $scroll = "5m";
        $scrollId = "foo,bar";

        $client = $this->createMock(Client::class);

        $client->expects($this->once())
            ->method("search")
            ->with(
                $this->equalTo(["size" => $size, "scroll" => $scroll]),
            )
            ->willReturn(
                EsUtility::response(
                    [
                        EsUtility::hit(1),
                        EsUtility::hit(2),
                        EsUtility::hit(3),
                    ],
                    $scrollId
                ),
            );

        $client->expects($this->exactly(2))
            ->method("scroll")
            ->withConsecutive(
                [$this->equalTo(["scroll" => $scroll, "body" => ["scroll_id" => $scrollId]])],
                [$this->equalTo(["scroll" => $scroll, "body" => ["scroll_id" => $scrollId]])],
            )
            ->willReturn(
                EsUtility::response([
                    EsUtility::hit(4),
                    EsUtility::hit(5),
                    EsUtility::hit(6),
                ]),
                EsUtility::response()
            );

        $client->expects($this->once())
            ->method("clearScroll")
            ->with(
                $this->equalTo(["body" => ["scroll_id" => $scrollId]]),
            );

        $cursorFactory = new EsScrollCursorFactory($client, 3, "5m");

        $hits = EsUtility::hitsId($cursorFactory->hits());
        $hits = iterator_to_array($hits);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $hits);
    }
}

<?php

namespace Nekman\EsPagination\CursorFactories;

use Elastic\Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;
use PHPUnit\Framework\TestCase;

class EsFromCursorFactoryTest extends TestCase
{
    public function testResponses()
    {
        $size = 3;

        $client = $this->createMock(Client::class);

        $client->expects($this->exactly(3))
            ->method("search")
            ->withConsecutive(
                [$this->equalTo(["size" => $size])],
                [$this->equalTo(["size" => $size, "from" => $size])],
                [$this->equalTo(["size" => $size, "from" => $size * 2])],
            )
            ->willReturn(
                EsUtility::response([
                    EsUtility::hit(1),
                    EsUtility::hit(2),
                    EsUtility::hit(3),
                ]),
                EsUtility::response([
                    EsUtility::hit(4),
                    EsUtility::hit(5),
                    EsUtility::hit(6),
                ]),
                EsUtility::response()
            );

        $cursorFactory = new EsFromCursorFactory($client, $size);

        $hits = EsUtility::hitsId($cursorFactory->hits());
        $hits = iterator_to_array($hits);

        $this->assertEquals([1, 2, 3, 4, 5, 6], $hits);
    }
}

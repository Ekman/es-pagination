<?php

namespace Nekman\EsPagination;

use Elasticsearch\Client;

/**
 * Deep paginate an ES search by using scroll
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html#scroll-search-results
 */
class EsScrollCursorFactory extends BaseCursorFactory
{
    private Client $client;
    private int $pageSize;
    private string $scrollDuration;

    public function __construct(
        Client $client,
        int $pageSize = 1000,
        string $scrollDuration = "1m"
    ) {
        $this->client = $client;
        $this->pageSize = $pageSize;
        $this->scrollDuration = $scrollDuration;
    }

    public function responses(array $params): iterable
    {
        $params = array_merge($params, [
            "scroll" => $this->scrollDuration,
            "size" => $params["size"] ?? $this->pageSize,
        ]);

        $response = $this->client->search($params);
        $scrollId = $response["_scroll_id"];

        try {
            while (EsUtility::countHits($response) > 0) {
                yield $response;

                $response = $this->client->scroll([
                    "scroll" => $this->scrollDuration,
                    "body" => [
                        "scroll_id" => $scrollId,
                    ],
                ]);
            }
        } finally {
            $this->client->clearScroll([
                "body" => [
                    "scroll_id" => $scrollId,
                ]
            ]);
        }
    }
}

<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;

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
        int $pageSize = EsUtility::DEFAULT_PAGE_SIZE,
        string $scrollDuration = "1m"
    ) {
        $this->client = $client;
        $this->pageSize = $pageSize;
        $this->scrollDuration = $scrollDuration;
    }

    public function responses(array $params = []): iterable
    {
        $params = array_merge($params, [
            "scroll" => $params["scroll"] ?? $this->scrollDuration,
            "size" => $params["size"] ?? $this->pageSize,
        ]);

        yield $response = $this->client->search($params);
        $scrollId = $response["_scroll_id"];

        try {
            while (EsUtility::countHits($response) >= $params["size"]) {
                yield $response = $this->client->scroll([
                    "scroll" => $params["scroll"] ?? $this->scrollDuration,
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

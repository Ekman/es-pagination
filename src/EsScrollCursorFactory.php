<?php

namespace Nekman\EsPagination;

use Elasticsearch\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Deep paginate an ES search by using scroll
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html#scroll-search-results
 */
class EsScrollCursorFactory implements EsCursorFactoryInterface
{
    private Client $client;
    private int $pageSize;
    private string $scrollDuration;
    private LoggerInterface $logger;

    public function __construct(
        Client $client,
        int $pageSize = 1000,
        string $scrollDuration = "1m",
        ?LoggerInterface $logger = null
    ) {
        $this->client = $client;
        $this->pageSize = $pageSize;
        $this->scrollDuration = $scrollDuration;
        $this->logger = $logger ?? new NullLogger();
    }

    public function hits(array $params): iterable
    {
        foreach ($this->responses($params) as $response) {
            foreach (EsUtility::hits($response) as $hit) {
                yield $hit;
            }
        }
    }

    public function responses(array $params): iterable
    {
        $params = array_merge($params, [
            "scroll" => $this->scrollDuration,
            "size" => $this->pageSize,
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

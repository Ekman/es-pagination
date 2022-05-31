<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;

class EsSearchAfterCursorFactory extends BaseCursorFactory
{
    private Client $client;
    private int $pageSize;

    public function __construct(
        Client $client,
        int $pageSize = EsUtility::DEFAULT_PAGE_SIZE,
    ) {
        $this->client = $client;
        $this->pageSize = $pageSize;
    }

    public function responses(array $params = []): iterable
    {
        if (!isset($params["size"])) {
            $params["size"] = $this->pageSize;
        }

        yield $response = $this->client->search($params);

        while (EsUtility::countHits($response) >= $params["size"]) {
            // The last hit of the response will contain information on how to get the next result set
            $lastHit = EsUtility::lastHit($response);

            if (!$lastHit || !isset($lastHit["sort"])) {
                break;
            }

            $params["body"]["search_after"] = $lastHit["sort"];

            yield $response = $this->client->search($params);
        }
    }
}

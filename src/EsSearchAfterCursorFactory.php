<?php

namespace Nekman\EsPagination;

use Elasticsearch\Client;

class EsSearchAfterCursorFactory extends BaseCursorFactory
{
    private Client $client;
    private int $pageSize;

    public function __construct(Client $client, int $pageSize = EsUtility::DEFAULT_PAGE_SIZE)
    {
        $this->client = $client;
        $this->pageSize = $pageSize;
    }

    public function responses(array $params): iterable
    {
        if (!isset($params["size"])) {
            $params["size"] = $this->pageSize;
        }

        $response = $this->client->search($params);

        while (EsUtility::countHits($response) > 0) {
            yield $response;

            // The last hit of the response will contain information on how to get the next result set
            $lastHit = EsUtility::lastHit($response);
            $params["body"]["search_after"] = $lastHit["sort"];

            $response = $this->client->search($params);
        }
    }
}

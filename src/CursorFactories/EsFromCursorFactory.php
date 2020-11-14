<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;

class EsFromCursorFactory extends BaseCursorFactory
{
    private Client $es;
    private int $pageSize;

    public function __construct(Client $es, int $pageSize = EsUtility::DEFAULT_PAGE_SIZE)
    {
        $this->es = $es;
        $this->pageSize = $pageSize;
    }

    public function responses(array $params = []): iterable
    {
        if (!isset($params["size"])) {
            $params["size"] = $this->pageSize;
        }

        $response = $this->es->search($params);

        while (EsUtility::countHits($response) > 0) {
            yield $response;

            $params["from"] = ($params["from"] ?? 0) + $params["size"];

            $response = $this->es->search($params);
        }
    }
}

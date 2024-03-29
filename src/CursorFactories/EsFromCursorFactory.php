<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;

final class EsFromCursorFactory extends BaseCursorFactory
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

        yield $response = $this->es->search($params);

        while (EsUtility::countHits($response) >= $params["size"]) {
            $params["from"] = ($params["from"] ?? 0) + $params["size"];

            yield $response = $this->es->search($params);
        }
    }
}

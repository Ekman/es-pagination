<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\EsUtility;

class EsSearchAfterCursorFactory extends BaseCursorFactory
{
    private Client $client;
    private int $pageSize;
    private array $defaultSort;
	private string $pitKeepAlive;

    public function __construct(
        Client $client,
        int $pageSize = EsUtility::DEFAULT_PAGE_SIZE,
        array $defaultSort = [["_id" => "asc"]],
		string $pitKeepAlive = "1m"
    ) {
        $this->client = $client;
        $this->pageSize = $pageSize;
        $this->defaultSort = $defaultSort;
		$this->pitKeepAlive = $pitKeepAlive;
    }

    public function responses(array $params = []): iterable
    {
        if (!isset($params["size"])) {
            $params["size"] = $this->pageSize;
        }

        // If no sorting was provided in the initial params, the last hit will have no sorting as well. Therefore,
        // we need to fallback to something that is similar to all entities which is an "_id". Note that ES
        // does not recommend using "_id" here, not much to do though.
        if (empty($params["body"]["sort"])) {
            $params = EsUtility::paramsSort($params, $this->defaultSort);
        }

		// Check if the user has provided a PIT ID. If not, create one and manage it for the user.
		// If the user has created an own PIT then let them manage it themselves.
		$pit = $params["pit"]["id"] ?? null;
		$managePit = false;

		if (!$pit) {
			if ($index = $params["index"] ?? null) {
				$this->client->openPointInTime([
					"index" => $index,
					"keep_alive" => $this->pitKeepAlive,
				]);
				$managePit = true;
				unset($params["index"]);
			}
		}

		$params["pit"] = [
			"id" => $pit,
			"keep_alive" => $params["pit"]["keep_alive"] ?? $this->pitKeepAlive,
		];

		try {
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
		} finally {
			if ($managePit) {
				@$this->client->closePointInTime([
					"body" => [
						"id" => $pit,
					],
				]);
			}
		}
    }
}

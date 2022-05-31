<?php

namespace Nekman\EsPagination\CursorFactories;

use Elasticsearch\Client;
use Nekman\EsPagination\Contracts\EsCursorFactoryInterface;
use Nekman\EsPagination\Exceptions\CreatePitException;

class EsPitCursorFactory extends BaseCursorFactory
{
    private EsCursorFactoryInterface $delegate;
    private Client $client;
    private string $pitKeepAlive;

    public function __construct(EsCursorFactoryInterface $delegate, Client $client, string $pitKeepAlive = "1m")
    {
        $this->delegate = $delegate;
        $this->client = $client;
        $this->pitKeepAlive = $pitKeepAlive;
    }

    public function responses(array $params = []): iterable
    {
        // Check if the user has provided a PIT ID. If not, create one and manage it for the user.
        // If the user has created an own PIT then let them manage it themselves.
        $pit = $params["pit"]["id"] ?? null;

        if (!$pit) {
            $index = $params["index"] ?? null;

            if (!$index) {
                throw new CreatePitException("Could not create a PIT due to \"index\" missing in \$params");
            }

            $response = $this->client->openPointInTime([
                "index" => $index,
                "keep_alive" => $this->pitKeepAlive,
            ]);

            $pit = $response["id"] ?? null;
            unset($params["index"]);
        }

        $params["pit"] = [
            "id" => $pit,
            "keep_alive" => $params["pit"]["keep_alive"] ?? $this->pitKeepAlive,
        ];

        try {
            return $this->delegate->responses($params);
        } finally {
            @$this->client->closePointInTime([
                "body" => [
                    "id" => $pit,
                ],
            ]);
        }
    }
}

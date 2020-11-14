<?php

namespace Nekman\EsPagination;

/** @internal */
class EsUtility
{
    /** @var int Default page size to use for all cursor factories */
    public const DEFAULT_PAGE_SIZE = 1000;

    /**
     * Count how many hits a response has
     * @param array $response Elasticsearch response
     * @return int Amount of hits inside the Elasticsearch response
     */
    public static function countHits(array $response): int
    {
        return count(self::hits($response));
    }

    /**
     * Get all hits from a response
     * @param array $response Elasticsearch response
     * @return array The hits inside the Elasticsearch response
     */
    public static function hits(array $response): array
    {
        return $response["hits"]["hits"] ?? [];
    }

    /**
     * Get the last hit from a response
     * @param array $response Elasticsearch response
     * @return array|null The last hit inside the Elasticsearch response, or null if no hits exists
     */
    public static function lastHit(array $response): ?array
    {
        $hits = self::hits($response);
        $nHits = count($hits);

        return $nHits === 0
            ? null
            : $hits[$nHits - 1];
    }

    /**
     * Set the sort on search parameters
     * @param array $params Elasticsearch search parameters
     * @param array $sort Sort to set on the parameters
     * @return array Elasticsearch search with sort parameters
     */
    public static function paramsSort(array $params, array $sort): array
    {
        $params["body"] = array_merge(
            $params["body"] ?? [],
            ["sort" => $sort]
        );

        return $params;
    }

    /**
     * Create a test Elasticsearch response. This function exists purely for the purpose of utilizing DRY
     * inside tests, it is not designed to make sense.
     * @param array $hits Search hits
     * @param string|null $scrollId Add a scroll ID to the response
     * @return array An test Elasticsearch response
     */
    public static function response(array $hits = [], ?string $scrollId = null): array
    {
        $response = [
            "hits" => [
                "hits" => $hits
            ]
        ];

        if ($scrollId) {
            $response["_scroll_id"] = $scrollId;
        }

        return $response;
    }

    /**
     * Get the IDs of a collection of Elasticsearch hits
     * @param iterable $hits A collection of Elasticsearch hits
     * @return iterable A collection of IDs
     */
    public static function hitsId(iterable $hits): iterable
    {
        foreach ($hits as $hit) {
            yield $hit["_id"];
        }
    }

    /**
     * Create a test Elasticsearch hit. This function exists purely for the purpose of utilizing DRY
     * inside tests, it is not designed to make sense.
     * @param string $id The ID of the hit
     * @param array $sort Sort parameter of the hit
     * @return array An test Elasticsearch hit
     */
    public static function hit(string $id, array $sort = []): array
    {
        $hit = ["_id" => $id];

        if (!empty($sort)) {
            $hit["sort"] = $sort;
        }

        return $hit;
    }
}

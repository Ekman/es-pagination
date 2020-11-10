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

    public static function setParamsSort(array $params, array $sort): array
    {
        $params["body"] = array_merge(
            $params["body"] ?? [],
            ["sort" => $sort]
        );

        return $params;
    }
}

<?php

namespace Nekman\EsPagination;

/** @internal */
class EsUtility
{
    public static function countHits(array $response): int
    {
        return count(self::hits($response));
    }

    public static function hits(array $response): array
    {
        return $response["hits"]["hits"] ?? [];
    }
}

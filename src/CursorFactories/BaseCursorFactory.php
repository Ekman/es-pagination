<?php

namespace Nekman\EsPagination\CursorFactories;

use Nekman\EsPagination\Contracts\EsCursorFactoryInterface;
use Nekman\EsPagination\EsUtility;

/** @internal */
abstract class BaseCursorFactory implements EsCursorFactoryInterface
{
    final public function hits(array $params = []): iterable
    {
        foreach ($this->responses($params) as $response) {
            foreach (EsUtility::hits($response) as $hit) {
                yield $hit;
            }
        }
    }
}

<?php

namespace Nekman\EsPagination\Contracts;

interface EsCursorFactoryInterface
{
    /**
     * Paginate an ES search by yielding each response
     * @param array $params ES search parameters
     * @return iterable Each response
	 * @throws EsPaginationExceptionInterface
     */
    public function responses(array $params = []): iterable;

    /**
     * Paginate an ES search by yielding each hit within a response
     * @param array $params ES search parameters
     * @return iterable Each hit within each response
	 * @throws EsPaginationExceptionInterface
     */
    public function hits(array $params = []): iterable;
}

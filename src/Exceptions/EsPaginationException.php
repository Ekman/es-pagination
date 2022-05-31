<?php

namespace Nekman\EsPagination\Exceptions;

use Nekman\EsPagination\Contracts\EsPaginationExceptionInterface;
use RuntimeException;

/**
 * @internal Not intended to be used outside the library. If you want to catch this, use the interface instead.
 * @see EsPaginationExceptionInterface
 */
class EsPaginationException extends RuntimeException implements EsPaginationExceptionInterface
{
}

<?php declare(strict_types=1);

namespace Elastic\Adapter\Exceptions;

use Exception;

final class RawResultReadOnlyException extends Exception
{
    public function __construct()
    {
        parent::__construct('Raw result can not be modified.');
    }
}

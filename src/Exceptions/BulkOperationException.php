<?php declare(strict_types=1);

namespace Elastic\Adapter\Exceptions;

use Exception;

final class BulkOperationException extends Exception
{
    private array $rawResult;

    public function __construct(array $rawResult)
    {
        $this->rawResult = $rawResult;

        parent::__construct($this->makeErrorMessage());
    }

    public function context(): array
    {
        return [
            'rawResult' => $this->rawResult,
        ];
    }

    public function rawResult(): array
    {
        return $this->rawResult;
    }

    private function makeErrorMessage(): string
    {
        $items = $this->rawResult['items'] ?? [];
        $count = count($items);

        $reason = sprintf(
            '%s did not complete successfully.',
            $count > 0 ? $count . ' bulk operation(s)' : 'One or more'
        );

        $failedOperations = $items[0] ?? [];
        $firstOperation = reset($failedOperations);
        $firstError = ($firstOperation ?? [])['error'] ?? null;

        if (isset($firstError['type'], $firstError['reason'])) {
            $reason .= sprintf(
                ' %s: %s. Reason: %s.',
                $count > 1 ? 'First error' : 'Error',
                $firstError['type'],
                $firstError['reason']
            );
        }

        return sprintf(
            '%s Catch the exception and use the %s::rawResult() method to get more details.',
            $reason,
            self::class
        );
    }
}

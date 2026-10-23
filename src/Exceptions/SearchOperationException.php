<?php declare(strict_types=1);

namespace Elastic\Adapter\Exceptions;

use Exception;

final class SearchOperationException extends Exception
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
        $responses = $this->rawResult['responses'] ?? [];
        $failed = array_filter($responses, static fn (array $response) => isset($response['error']));
        $count = count($failed);

        $reason = sprintf(
            '%s did not complete successfully.',
            $count > 0 ? $count . ' search(es)' : 'One or more searches'
        );

        $position = array_key_first($failed);
        $firstError = $position !== null ? $failed[$position]['error'] : null;

        if (isset($firstError['type'], $firstError['reason'])) {
            $reason .= sprintf(
                ' %s at position %d: %s. Reason: %s.',
                $count > 1 ? 'First error' : 'Error',
                $position,
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

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
        $error = $this->rawResult['error'] ?? [];

        $reason = isset($error['type'], $error['reason'])
            ? sprintf('One of the searches failed. Error: %s. Reason: %s.', $error['type'], $error['reason'])
            : 'One of the searches did not complete successfully.';

        return sprintf(
            '%s Catch the exception and use the %s::rawResult() method to get more details.',
            $reason,
            self::class
        );
    }
}

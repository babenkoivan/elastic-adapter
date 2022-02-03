<?php declare(strict_types=1);

namespace ElasticAdapter\Exceptions;

use ErrorException;

final class BulkRequestException extends ErrorException
{
    /**
     * @var array
     */
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;

        parent::__construct($this->makeErrorFromResponse());
    }

    public function context(): array
    {
        return [
            'response' => $this->getResponse(),
        ];
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    private function makeErrorFromResponse(): string
    {
        $items = $this->response['items'] ?? [];
        $count = count($items);

        $reason = sprintf('%s did not complete successfully.', $count > 0 ? $count . ' bulk operation(s)' : 'One or more');

        $failedOperations = $items[0] ?? [];
        $firstOperation = reset($failedOperations);
        $firstError = ($firstOperation ?? [])['error'] ?? null;

        if (isset($firstError) && isset($firstError['type']) && isset($firstError['reason'])) {
            $reason .= sprintf(' %s: %s. Reason: %s.', $count > 1 ? 'First error' : 'Error', $firstError['type'], $firstError['reason']);
        }

        return sprintf('%s Catch the exception and use the %s::getResponse() method to get more details.', $reason, self::class);
    }
}

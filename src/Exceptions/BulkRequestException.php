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

        parent::__construct(
            ($this->getFirstErrorFromResponse() ?? 'One or more bulk operations did not complete successfully.') .
            ' Catch the exception and use the BulkRequestException::getResponse() method to get more details.'
        );
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getResponseItems(): array
    {
        return $this->response['items'] ?? [];
    }

    private function getFirstErrorFromResponse(): ?string
    {
        $items = $this->getResponseItems();

        foreach ($items as $item) {
            foreach ($item ?? [] as $response) {
                $count = count($items);
                $type = $response['error']['type'] ?? 'NULL';
                $reason = $response['error']['reason'] ?? 'NULL';

                return "$count bulk operation(s) did not complete successfully. " .
                    ($count > 1 ? "First error: " : "Error: ") .
                    "$type. Reason: $reason.";
            }
        }

        return null;
    }
}

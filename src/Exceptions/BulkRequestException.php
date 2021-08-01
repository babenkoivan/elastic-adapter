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
        parent::__construct(
            'One or more bulk operations did not complete successfully. ' .
            'Catch the exception and use the BulkRequestException::getResponse() method to get more details.'
        );

        $this->response = $response;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}

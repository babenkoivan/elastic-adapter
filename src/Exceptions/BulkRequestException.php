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
        parent::__construct('One or more operations in the bulk request did not complete successfully');
        $this->response = $response;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}

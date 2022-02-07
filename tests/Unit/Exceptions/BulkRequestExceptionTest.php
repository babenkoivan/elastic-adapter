<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Exceptions;

use ElasticAdapter\Exceptions\BulkRequestException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Exceptions\BulkRequestException
 */
final class BulkRequestExceptionTest extends TestCase
{
    public function test_response_can_be_retrieved(): void
    {
        $response = [
            'took' => 486,
            'errors' => true,
            'items' => [
                [
                    'update' => [
                        '_index' => 'index1',
                        '_type' => '_doc',
                        '_id' => '5',
                        'status' => 404,
                        'error' => [
                            'type' => 'document_missing_exception',
                            'reason' => '[_doc][5]: document missing',
                            'index_uuid' => 'aAsFqTI0Tc2W0LCWgPNrOA',
                            'shard' => '0',
                            'index' => 'index1',
                        ],
                    ],
                ],
            ],
        ];

        $exception = new BulkRequestException($response);

        $this->assertSame($response, $exception->getResponse());
    }

    public function test_first_error_message_from_response_is_given_in_exception_message(): void
    {
        $response = [
            'took' => 486,
            'errors' => true,
            'items' => [
                [
                    'update' => [
                        '_index' => 'index1',
                        '_type' => '_doc',
                        '_id' => '5',
                        'status' => 404,
                        'error' => [
                            'type' => 'document_missing_exception',
                            'reason' => '[_doc][5]: document missing',
                            'index_uuid' => 'aAsFqTI0Tc2W0LCWgPNrOA',
                            'shard' => '0',
                            'index' => 'index1',
                        ],
                    ],
                ],
            ],
        ];

        $exception = new BulkRequestException($response);

        $this->assertEquals(
            '1 bulk operation(s) did not complete successfully. Error: document_missing_exception. Reason: [_doc][5]: document missing. Catch the exception and use the ElasticAdapter\Exceptions\BulkRequestException::getResponse() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_throw_with_many_errors_in_response(): void
    {
        $response = [
            'took' => 486,
            'errors' => true,
            'items' => [
                [
                    'update' => [
                        '_index' => 'index1',
                        '_type' => '_doc',
                        '_id' => '5',
                        'status' => 404,
                        'error' => [
                            'type' => 'document_missing_exception',
                            'reason' => '[_doc][5]: document missing',
                            'index_uuid' => 'aAsFqTI0Tc2W0LCWgPNrOA',
                            'shard' => '0',
                            'index' => 'index1',
                        ],
                    ],
                ],
                [
                    'index' => [
                        '_index' => 'index1',
                        '_type' => '_doc',
                        '_id' => '5',
                        'status' => 404,
                        'error' => [
                            'type' => 'mapper_parsing_exception',
                            'reason' => 'failed to parse field',
                            'index_uuid' => 'aAsFqTI0Tc2W0LCWgPNrOA',
                            'shard' => '0',
                            'index' => 'index1',
                        ],
                    ],
                ],
            ],
        ];

        $exception = new BulkRequestException($response);

        $this->assertEquals(
            '2 bulk operation(s) did not complete successfully. First error: document_missing_exception. Reason: [_doc][5]: document missing. Catch the exception and use the ElasticAdapter\Exceptions\BulkRequestException::getResponse() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_throw_with_missing_error_in_response(): void
    {
        $response = [
            'took' => 486,
            'errors' => true,
            'items' => [
                [
                    'update' => [
                        '_index' => 'index1',
                        '_type' => '_doc',
                        '_id' => '5',
                        'status' => 404,
                    ],
                ],
            ],
        ];

        $exception = new BulkRequestException($response);

        $this->assertEquals(
            '1 bulk operation(s) did not complete successfully. Catch the exception and use the ElasticAdapter\Exceptions\BulkRequestException::getResponse() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_throw_with_missing_items_in_response(): void
    {
        $response = [
            'took' => 486,
            'errors' => true,
            'items' => [],
        ];

        $exception = new BulkRequestException($response);

        $this->assertEquals(
            'One or more did not complete successfully. Catch the exception and use the ElasticAdapter\Exceptions\BulkRequestException::getResponse() method to get more details.',
            $exception->getMessage()
        );
    }
}

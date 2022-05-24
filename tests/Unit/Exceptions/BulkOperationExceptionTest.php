<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Exceptions;

use Elastic\Adapter\Exceptions\BulkOperationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Exceptions\BulkOperationException
 */
final class BulkOperationExceptionTest extends TestCase
{
    public function test_raw_result_can_be_retrieved(): void
    {
        $rawResult = [
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

        $exception = new BulkOperationException($rawResult);

        $this->assertSame($rawResult, $exception->rawResult());
    }

    public function test_first_error_message_from_result_is_given_in_exception_message(): void
    {
        $rawResult = [
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

        $exception = new BulkOperationException($rawResult);

        $this->assertEquals(
            '1 bulk operation(s) did not complete successfully. Error: document_missing_exception. Reason: [_doc][5]: document missing. Catch the exception and use the Elastic\Adapter\Exceptions\BulkOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_throw_with_many_errors_in_result(): void
    {
        $rawResult = [
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

        $exception = new BulkOperationException($rawResult);

        $this->assertEquals(
            '2 bulk operation(s) did not complete successfully. First error: document_missing_exception. Reason: [_doc][5]: document missing. Catch the exception and use the Elastic\Adapter\Exceptions\BulkOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_throw_with_missing_error_in_result(): void
    {
        $rawResult = [
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

        $exception = new BulkOperationException($rawResult);

        $this->assertEquals(
            '1 bulk operation(s) did not complete successfully. Catch the exception and use the Elastic\Adapter\Exceptions\BulkOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_throw_with_missing_items_in_result(): void
    {
        $rawResult = [
            'took' => 486,
            'errors' => true,
            'items' => [],
        ];

        $exception = new BulkOperationException($rawResult);

        $this->assertEquals(
            'One or more did not complete successfully. Catch the exception and use the Elastic\Adapter\Exceptions\BulkOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }
}

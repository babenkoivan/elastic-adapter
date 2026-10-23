<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Exceptions;

use Elastic\Adapter\Exceptions\SearchOperationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchOperationException::class)]
final class SearchOperationExceptionTest extends TestCase
{
    public function test_raw_result_can_be_retrieved(): void
    {
        $rawResult = [
            'took' => 5,
            'responses' => [
                [
                    'error' => [
                        'type' => 'index_not_found_exception',
                        'reason' => 'no such index [missing]',
                    ],
                    'status' => 404,
                ],
            ],
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertSame($rawResult, $exception->rawResult());
    }

    public function test_error_message_from_result_is_given_in_exception_message(): void
    {
        $rawResult = [
            'took' => 5,
            'responses' => [
                [
                    'hits' => ['total' => ['value' => 0, 'relation' => 'eq'], 'max_score' => null, 'hits' => []],
                    'status' => 200,
                ],
                [
                    'error' => [
                        'type' => 'index_not_found_exception',
                        'reason' => 'no such index [missing]',
                    ],
                    'status' => 404,
                ],
            ],
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertEquals(
            '1 search(es) did not complete successfully. Error at position 1: index_not_found_exception. Reason: no such index [missing]. Catch the exception and use the Elastic\Adapter\Exceptions\SearchOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_thrown_with_many_errors_in_result(): void
    {
        $rawResult = [
            'took' => 5,
            'responses' => [
                [
                    'hits' => ['total' => ['value' => 0, 'relation' => 'eq'], 'max_score' => null, 'hits' => []],
                    'status' => 200,
                ],
                [
                    'error' => [
                        'type' => 'index_not_found_exception',
                        'reason' => 'no such index [missing]',
                    ],
                    'status' => 404,
                ],
                [
                    'error' => [
                        'type' => 'search_phase_execution_exception',
                        'reason' => 'all shards failed',
                    ],
                    'status' => 400,
                ],
            ],
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertEquals(
            '2 search(es) did not complete successfully. First error at position 1: index_not_found_exception. Reason: no such index [missing]. Catch the exception and use the Elastic\Adapter\Exceptions\SearchOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_thrown_with_missing_error_details_in_result(): void
    {
        $rawResult = [
            'took' => 5,
            'responses' => [
                [
                    'error' => [],
                    'status' => 500,
                ],
            ],
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertEquals(
            '1 search(es) did not complete successfully. Catch the exception and use the Elastic\Adapter\Exceptions\SearchOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_thrown_with_missing_responses_in_result(): void
    {
        $rawResult = [
            'took' => 5,
            'responses' => [],
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertEquals(
            'One or more searches did not complete successfully. Catch the exception and use the Elastic\Adapter\Exceptions\SearchOperationException::rawResult() method to get more details.',
            $exception->getMessage()
        );
    }
}

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
            'error' => [
                'type' => 'index_not_found_exception',
                'reason' => 'no such index [missing]',
            ],
            'status' => 404,
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertSame($rawResult, $exception->rawResult());
    }

    public function test_error_message_from_result_is_given_in_exception_message(): void
    {
        $rawResult = [
            'error' => [
                'type' => 'index_not_found_exception',
                'reason' => 'no such index [missing]',
            ],
            'status' => 404,
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertEquals(
            'One of the searches failed. Error: index_not_found_exception. Reason: no such index [missing]. ' .
            'Catch the exception and use the Elastic\Adapter\Exceptions\SearchOperationException::rawResult() ' .
            'method to get more details.',
            $exception->getMessage()
        );
    }

    public function test_exception_can_be_thrown_with_missing_error_in_result(): void
    {
        $rawResult = [
            'status' => 500,
        ];

        $exception = new SearchOperationException($rawResult);

        $this->assertEquals(
            'One of the searches did not complete successfully. ' .
            'Catch the exception and use the Elastic\Adapter\Exceptions\SearchOperationException::rawResult() ' .
            'method to get more details.',
            $exception->getMessage()
        );
    }
}

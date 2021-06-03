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
}

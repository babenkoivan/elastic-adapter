<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Search;

use Elastic\Adapter\Search\Aggregation;
use Elastic\Adapter\Search\Bucket;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Search\Aggregation
 *
 * @uses   \Elastic\Adapter\Search\Bucket
 */
final class AggregationTest extends TestCase
{
    private Aggregation $aggregation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aggregation = new Aggregation([
            'doc_count_error_upper_bound' => 0,
            'sum_other_doc_count' => 0,
            'buckets' => [
                [
                    'key' => 'electronic',
                    'doc_count' => 6,
                ],
            ],
        ]);
    }

    public function test_buckets_can_be_retrieved(): void
    {
        $this->assertEquals(
            collect([
                new Bucket([
                    'key' => 'electronic',
                    'doc_count' => 6,
                ]),
            ]),
            $this->aggregation->buckets()
        );
    }

    public function test_bucket_keys_can_be_plucked(): void
    {
        $this->assertEquals(
            collect(['electronic']),
            $this->aggregation->buckets()->pluck('key')
        );
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $this->assertSame([
            'doc_count_error_upper_bound' => 0,
            'sum_other_doc_count' => 0,
            'buckets' => [
                [
                    'key' => 'electronic',
                    'doc_count' => 6,
                ],
            ],
        ], $this->aggregation->raw());
    }
}

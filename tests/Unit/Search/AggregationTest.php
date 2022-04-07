<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\Aggregation;
use ElasticAdapter\Search\Bucket;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\Aggregation
 *
 * @uses   \ElasticAdapter\Search\Bucket
 */
final class AggregationTest extends TestCase
{
    /**
     * @var Aggregation
     */
    private $aggregation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aggregation = new Aggregation([
            'doc_count_error_upper_bound' => 0,
            'sum_other_doc_count' => 0,
            'max_price' => 1111,
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
        $this->assertEquals(collect([
            new Bucket([
                'key' => 'electronic',
                'doc_count' => 6,
            ]),
        ]), $this->aggregation->buckets());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $this->assertSame([
            'doc_count_error_upper_bound' => 0,
            'sum_other_doc_count' => 0,
            'max_price' => 1111,
            'buckets' => [
                [
                    'key' => 'electronic',
                    'doc_count' => 6,
                ],
            ],
        ], $this->aggregation->raw());
    }

    public function test_aggregation_get_value_success(): void
    {
        $this->assertEquals(
            1111,
            $this->aggregation->max_price
        );
    }

    public function test_aggregation_get_value_is_null(): void
    {
        $this->assertSame(
            null,
            $this->aggregation->aaa
        );
    }
    
}

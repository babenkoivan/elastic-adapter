<?php
declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\SearchResponse
 * @uses   \ElasticAdapter\Search\Hit
 */
final class SearchResponseTest extends TestCase
{
    public function test_hits_can_be_retrieved(): void
    {
        $searchResponse = new SearchResponse([
            'hits' => [
                'hits' => [
                    [
                        '_id' => '1',
                        '_source' => ['title' => 'foo']
                    ]
                ]
            ]
        ]);

        $this->assertEquals(
            [new Hit(['_id' => '1', '_source' => ['title' => 'foo']])],
            $searchResponse->getHits()
        );
    }

    public function test_total_number_of_hits_can_be_retrieved(): void
    {
        $searchResponse = new SearchResponse([
            'hits' => [
                'total' => ['value' => 100]
            ]
        ]);

        $this->assertSame(100, $searchResponse->getHitsTotal());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $searchResponse = new SearchResponse([
            'hits' => [
                'total' => ['value' => 100],
                'hits' => [
                    [
                        '_id' => '1',
                        '_source' => ['title' => 'foo']
                    ],
                    [
                        '_id' => '2',
                        '_source' => ['title' => 'bar']
                    ]
                ]
            ]
        ]);

        $this->assertSame([
            'hits' => [
                'total' => ['value' => 100],
                'hits' => [
                    [
                        '_id' => '1',
                        '_source' => ['title' => 'foo']
                    ],
                    [
                        '_id' => '2',
                        '_source' => ['title' => 'bar']
                    ]
                ]
            ]
        ], $searchResponse->getRaw());
    }
}

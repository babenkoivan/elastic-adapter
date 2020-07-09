<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchResponse;
use ElasticAdapter\Search\Suggestion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\SearchResponse
 *
 * @uses   \ElasticAdapter\Search\Hit
 * @uses   \ElasticAdapter\Search\Suggestion
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
                        '_source' => ['title' => 'foo'],
                    ],
                ],
            ],
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
                'total' => ['value' => 100],
            ],
        ]);

        $this->assertSame(100, $searchResponse->getHitsTotal());
    }

    public function test_empty_array_is_returned_when_suggestions_are_not_present(): void
    {
        $searchResponse = new SearchResponse([
            'hits' => [],
        ]);

        $this->assertSame([], $searchResponse->getSuggestions());
    }

    public function test_suggestions_can_be_retrieved(): void
    {
        $searchResponse = new SearchResponse([
            'hits' => [],
            'suggest' => [
                'color_suggestion' => [
                    [
                        'text' => 'red',
                        'offset' => 0,
                        'length' => 3,
                        'options' => [],
                    ],
                    [
                        'text' => 'blue',
                        'offset' => 4,
                        'length' => 4,
                        'options' => [],
                    ],
                ],
            ],
        ]);

        $this->assertEquals([
            'color_suggestion' => [
                new Suggestion([
                    'text' => 'red',
                    'offset' => 0,
                    'length' => 3,
                    'options' => [],
                ]),
                new Suggestion([
                    'text' => 'blue',
                    'offset' => 4,
                    'length' => 4,
                    'options' => [],
                ]),
            ],
        ], $searchResponse->getSuggestions());
    }

    public function test_aggregations_can_be_retrieved(): void
    {
        $searchResponse = new SearchResponse([
            'hits' => [],
            'aggregations' => [
                'min_price' => [
                    'value' => 10,
                ],
            ],
        ]);

        $this->assertEquals([
            'min_price' => [
                'value' => 10,
            ],
        ], $searchResponse->getAggregations());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $searchResponse = new SearchResponse([
            'hits' => [
                'total' => ['value' => 100],
                'hits' => [
                    [
                        '_id' => '1',
                        '_source' => ['title' => 'foo'],
                    ],
                    [
                        '_id' => '2',
                        '_source' => ['title' => 'bar'],
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            'hits' => [
                'total' => ['value' => 100],
                'hits' => [
                    [
                        '_id' => '1',
                        '_source' => ['title' => 'foo'],
                    ],
                    [
                        '_id' => '2',
                        '_source' => ['title' => 'bar'],
                    ],
                ],
            ],
        ], $searchResponse->getRaw());
    }
}

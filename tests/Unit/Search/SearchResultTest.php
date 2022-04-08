<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\Aggregation;
use ElasticAdapter\Search\Hit;
use ElasticAdapter\Search\SearchResult;
use ElasticAdapter\Search\Suggestion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\SearchResult
 *
 * @uses   \ElasticAdapter\Search\Aggregation
 * @uses   \ElasticAdapter\Search\Hit
 * @uses   \ElasticAdapter\Search\Suggestion
 */
final class SearchResultTest extends TestCase
{
    public function test_hits_can_be_retrieved(): void
    {
        $searchResult = new SearchResult([
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
            collect([new Hit(['_id' => '1', '_source' => ['title' => 'foo']])]),
            $searchResult->hits()
        );
    }

    public function test_total_number_of_hits_can_be_retrieved(): void
    {
        $searchResult = new SearchResult([
            'hits' => [
                'total' => ['value' => 100],
            ],
        ]);

        $this->assertSame(100, $searchResult->total());
    }

    public function test_empty_collection_is_returned_when_suggestions_are_not_present(): void
    {
        $searchResult = new SearchResult([
            'hits' => [],
        ]);

        $this->assertEquals(collect(), $searchResult->suggestions());
    }

    public function test_suggestions_can_be_retrieved(): void
    {
        $searchResult = new SearchResult([
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

        $this->assertEquals(collect([
            'color_suggestion' => collect([
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
            ]),
        ]), $searchResult->suggestions());
    }

    public function test_aggregations_can_be_retrieved(): void
    {
        $searchResult = new SearchResult([
            'hits' => [],
            'aggregations' => [
                'min_price' => [
                    'value' => 10,
                ],
            ],
        ]);

        $this->assertEquals(collect([
            'min_price' => new Aggregation([
                'value' => 10,
            ]),
        ]), $searchResult->aggregations());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $searchResult = new SearchResult([
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
        ], $searchResult->raw());
    }
}

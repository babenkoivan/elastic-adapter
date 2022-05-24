<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Search;

use Elastic\Adapter\Exceptions\RawResultReadOnlyException;
use Elastic\Adapter\Search\Aggregation;
use Elastic\Adapter\Search\Hit;
use Elastic\Adapter\Search\SearchResult;
use Elastic\Adapter\Search\Suggestion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Search\SearchResult
 *
 * @uses   \Elastic\Adapter\Exceptions\RawResultReadOnlyException
 * @uses   \Elastic\Adapter\Search\Aggregation
 * @uses   \Elastic\Adapter\Search\Hit
 * @uses   \Elastic\Adapter\Search\Suggestion
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
        ]), $searchResult->suggestions()->get('color_suggestion'));
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

        $this->assertEquals(
            new Aggregation(['value' => 10]),
            $searchResult->aggregations()->get('min_price')
        );
    }

    public function test_raw_total_can_be_retrieved(): void
    {
        $searchResult = new SearchResult([
            'hits' => [
                'total' => ['value' => 1],
            ],
        ]);

        /** @var array $hits */
        $hits = $searchResult['hits'];
        $this->assertSame(['value' => 1], $hits['total']);
    }

    /**
     * @noinspection OnlyWritesOnParameterInspection
     */
    public function test_raw_data_can_not_be_modified(): void
    {
        $this->expectException(RawResultReadOnlyException::class);

        $searchResult = new SearchResult([]);
        $searchResult['total'] = 100;
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

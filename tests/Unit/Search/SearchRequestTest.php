<?php

declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\SearchRequest;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticAdapter\Search\SearchRequest
 */
final class SearchRequestTest extends TestCase
{
    public function test_array_casting_with_query(): void
    {
        $request = new SearchRequest([
            'term' => [
                'user' => 'foo',
            ],
        ]);

        $this->assertSame([
            'body' => [
                'query' => [
                    'term' => [
                        'user' => 'foo',
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_query_and_highlight(): void
    {
        $request = new SearchRequest([
            'match' => [
                'content' => 'foo',
            ],
        ]);

        $request->highlight([
            'fields' => [
                'content' => new stdClass(),
            ],
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match' => [
                        'content' => 'foo',
                    ],
                ],
                'highlight' => [
                    'fields' => [
                        'content' => new stdClass(),
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_query_and_sort(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->sort([
            ['title' => 'asc'],
            '_score',
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'sort' => [
                    ['title' => 'asc'],
                    '_score',
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_query_and_rescore(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->rescore([
            'window_size' => 50,
            'query' => [
                'rescore_query' => [
                    'match_phrase' => [
                        'message' => [
                            'query' => 'the quick brown',
                            'slop' => 2,
                        ],
                    ],
                ],
                'query_weight' => 0.7,
                'rescore_query_weight' => 1.2,
            ],
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'rescore' => [
                    'window_size' => 50,
                    'query' => [
                        'rescore_query' => [
                            'match_phrase' => [
                                'message' => [
                                    'query' => 'the quick brown',
                                    'slop' => 2,
                                ],
                            ],
                        ],
                        'query_weight' => 0.7,
                        'rescore_query_weight' => 1.2,
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_query_and_from(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->from(10);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'from' => 10,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_query_and_size(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->size(100);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'size' => 100,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_query_and_suggest(): void
    {
        $request = new SearchRequest([
            'match_none' => new stdClass(),
        ]);

        $request->suggest([
            'color_suggestion' => [
                'text' => 'red',
                'term' => [
                    'field' => 'color',
                ],
            ],
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_none' => new stdClass(),
                ],
                'suggest' => [
                    'color_suggestion' => [
                        'text' => 'red',
                        'term' => [
                            'field' => 'color',
                        ],
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function sourceProvider(): array
    {
        return [
            [false],
            ['obj1.*'],
            [['obj1.*', 'obj2.*']],
            [['includes' => ['obj1.*', 'obj2.*'], 'excludes' => ['*.description']]],
        ];
    }

    /**
     * @dataProvider sourceProvider
     *
     * @param array|string|bool $source
     */
    public function test_array_casting_with_source($source): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->source($source);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                '_source' => $source,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_collapse(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->collapse([
            'field' => 'user',
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'collapse' => [
                    'field' => 'user',
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_aggregations(): void
    {
        $request = new SearchRequest();

        $request->aggregations([
            'min_price' => [
                'min' => [
                    'field' => 'price',
                ],
            ],
        ]);

        $this->assertEquals([
            'body' => [
                'aggregations' => [
                    'min_price' => [
                        'min' => [
                            'field' => 'price',
                        ],
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_post_filter(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->postFilter([
            'term' => [
                'color' => 'red',
            ],
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'post_filter' => [
                    'term' => [
                        'color' => 'red',
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_track_total_hits(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->trackTotalHits(100);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'track_total_hits' => 100,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_indices_boost(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->indicesBoost([
            ['my-alias' => 1.4],
            ['my-index' => 1.3],
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'indices_boost' => [
                    ['my-alias' => 1.4],
                    ['my-index' => 1.3],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_track_scores(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->trackScores(true);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'track_scores' => true,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_script_fields(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->scriptFields([
            'my_doubled_field' => [
                'script' => [
                    'lang' => 'painless',
                    'source' => 'doc[params.field] * params.multiplier',
                    'params' => [
                        'field' => 'my_field',
                        'multiplier' => 2,
                    ],
                ],
            ],
        ]);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'script_fields' => [
                    'my_doubled_field' => [
                        'script' => [
                            'lang' => 'painless',
                            'source' => 'doc[params.field] * params.multiplier',
                            'params' => [
                                'field' => 'my_field',
                                'multiplier' => 2,
                            ],
                        ],
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_min_score(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->minScore(0.5);

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
                'min_score' => 0.5,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_search_type(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->searchType('query_then_fetch');

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
            ],
            'search_type' => 'query_then_fetch',
        ], $request->toArray());
    }

    public function test_array_casting_with_preference(): void
    {
        $request = new SearchRequest([
            'match_all' => new stdClass(),
        ]);

        $request->preference('_local');

        $this->assertEquals([
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
            ],
            'preference' => '_local',
        ], $request->toArray());
    }
}

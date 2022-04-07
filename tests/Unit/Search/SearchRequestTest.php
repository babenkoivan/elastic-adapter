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
        $request = (new SearchRequest())->query([
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

    public function test_array_casting_with_highlight(): void
    {
        $request = (new SearchRequest())->highlight([
            'fields' => [
                'content' => new stdClass(),
            ],
        ]);

        $this->assertEquals([
            'body' => [
                'highlight' => [
                    'fields' => [
                        'content' => new stdClass(),
                    ],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_sort(): void
    {
        $request = (new SearchRequest())->sort([
            ['title' => 'asc'],
            '_score',
        ]);

        $this->assertEquals([
            'body' => [
                'sort' => [
                    ['title' => 'asc'],
                    '_score',
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_rescore(): void
    {
        $request = (new SearchRequest())->rescore([
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

    public function test_array_casting_with_from(): void
    {
        $request = (new SearchRequest())->from(10);

        $this->assertEquals([
            'body' => [
                'from' => 10,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_size(): void
    {
        $request = (new SearchRequest())->size(100);

        $this->assertEquals([
            'body' => [
                'size' => 100,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_suggest(): void
    {
        $request = (new SearchRequest())->suggest([
            'color_suggestion' => [
                'text' => 'red',
                'term' => [
                    'field' => 'color',
                ],
            ],
        ]);

        $this->assertEquals([
            'body' => [
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
        $request = (new SearchRequest())->source($source);

        $this->assertEquals([
            'body' => [
                '_source' => $source,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_collapse(): void
    {
        $request = (new SearchRequest())->collapse([
            'field' => 'user',
        ]);

        $this->assertEquals([
            'body' => [
                'collapse' => [
                    'field' => 'user',
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_aggregations(): void
    {
        $request = (new SearchRequest())->aggregations([
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
        $request = (new SearchRequest())->postFilter([
            'term' => [
                'color' => 'red',
            ],
        ]);

        $this->assertEquals([
            'body' => [
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
        $request = (new SearchRequest())->trackTotalHits(100);

        $this->assertEquals([
            'body' => [
                'track_total_hits' => 100,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_indices_boost(): void
    {
        $request = (new SearchRequest())->indicesBoost([
            ['my-alias' => 1.4],
            ['my-index' => 1.3],
        ]);

        $this->assertEquals([
            'body' => [
                'indices_boost' => [
                    ['my-alias' => 1.4],
                    ['my-index' => 1.3],
                ],
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_track_scores(): void
    {
        $request = (new SearchRequest())->trackScores(true);

        $this->assertEquals([
            'body' => [
                'track_scores' => true,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_script_fields(): void
    {
        $request = (new SearchRequest())->scriptFields([
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
        $request = (new SearchRequest())->minScore(0.5);

        $this->assertEquals([
            'body' => [
                'min_score' => 0.5,
            ],
        ], $request->toArray());
    }

    public function test_array_casting_with_search_type(): void
    {
        $request = (new SearchRequest())->searchType('query_then_fetch');

        $this->assertEquals([
            'search_type' => 'query_then_fetch',
        ], $request->toArray());
    }

    public function test_array_casting_with_preference(): void
    {
        $request = (new SearchRequest())->preference('_local');

        $this->assertEquals([
            'preference' => '_local',
        ], $request->toArray());
    }
}

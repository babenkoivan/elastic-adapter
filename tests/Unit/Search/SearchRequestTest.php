<?php
declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\SearchRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\SearchRequest
 */
final class SearchRequestTest extends TestCase
{
    public function test_default_array_conversion(): void
    {
        $request = new SearchRequest([
            'term' => ['user' => 'foo']
        ]);

        $this->assertSame([
            'query' => [
                'term' => ['user' => 'foo']
            ]
        ], $request->toArray());
    }

    public function test_configured_array_conversion(): void
    {
        $request = new SearchRequest([
            'match' => ['content' => 'foo']
        ]);

        $request->setHighlight([
            'fields' => [
                'content' => []
            ]
        ]);

        $request->setSort([
            ['title' => 'asc'],
            '_score'
        ]);

        $request->setSuggest(['by_term' => [
            'text' => 'foo',
            'term' => [
                'field' => 'content',
            ],
        ]]);

        $request->setFrom(0);
        $request->setSize(10);

        $this->assertSame([
            'query' => [
                'match' => ['content' => 'foo']
            ],
            'highlight' => [
                'fields' => [
                    'content' => []
                ]
            ],
            'sort' => [
                ['title' => 'asc'],
                '_score'
            ],
            'from' => 0,
            'size' => 10,
            'suggest' => [
                'by_term' => [
                    'text' => 'foo',
                    'term' => [
                        'field' => 'content',
                    ],
                ],
            ],
        ], $request->toArray());
    }
}

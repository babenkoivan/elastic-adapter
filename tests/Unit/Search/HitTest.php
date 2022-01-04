<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticAdapter\Search\Hit;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\Hit
 *
 * @uses   \ElasticAdapter\Documents\Document
 * @uses   \ElasticAdapter\Search\Highlight
 */
final class HitTest extends TestCase
{
    /**
     * @var Hit
     */
    private $hit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hit = new Hit([
            '_id' => '1',
            '_index' => 'test',
            '_source' => [
                'title' => 'foo',
            ],
            '_score' => 1.3,
            'highlight' => [
                'title' => [
                    ' <em>foo</em> ',
                ],
            ],
            'inner_hits' => [
                'nested' => [
                    'hits' => [
                        'total' => [
                            'value' => 1,
                        ],
                        'hits' => [
                            [
                                '_id' => '2',
                                '_index' => 'test',
                                '_source' => [
                                    'name' => 'bar',
                                ],
                                '_score' => 1.6,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_index_name_can_be_retrieved(): void
    {
        $this->assertSame('test', $this->hit->indexName());
    }

    public function test_document_can_be_retrieved(): void
    {
        $this->assertEquals(
            new Document('1', ['title' => 'foo']),
            $this->hit->document()
        );
    }

    public function test_highlight_can_be_retrieved_if_present(): void
    {
        $this->assertEquals(
            new Highlight(['title' => [' <em>foo</em> ']]),
            $this->hit->highlight()
        );
    }

    public function test_nothing_is_returned_when_trying_to_retrieve_highlight_but_it_is_not_present(): void
    {
        $hit = new Hit(['_id' => '1']);

        $this->assertNull($hit->highlight());
    }

    public function test_score_can_be_retrieved(): void
    {
        $this->assertSame(1.3, $this->hit->score());
    }

    public function test_inner_hits_can_be_retrieved(): void
    {
        $innerHit = new Hit([
            '_id' => '2',
            '_index' => 'test',
            '_source' => [
                'name' => 'bar',
            ],
            '_score' => 1.6,
        ]);

        $nestedInnerHits = $this->hit->innerHits()->get('nested');

        $this->assertCount(1, $nestedInnerHits);
        $this->assertEquals($innerHit, $nestedInnerHits->first());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $this->assertSame([
            '_id' => '1',
            '_index' => 'test',
            '_source' => [
                'title' => 'foo',
            ],
            '_score' => 1.3,
            'highlight' => [
                'title' => [
                    ' <em>foo</em> ',
                ],
            ],
            'inner_hits' => [
                'nested' => [
                    'hits' => [
                        'total' => [
                            'value' => 1,
                        ],
                        'hits' => [
                            [
                                '_id' => '2',
                                '_index' => 'test',
                                '_source' => [
                                    'name' => 'bar',
                                ],
                                '_score' => 1.6,
                            ],
                        ],
                    ],
                ],
            ],
        ], $this->hit->raw());
    }
}

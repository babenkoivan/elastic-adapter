<?php
declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Search\Highlight;
use ElasticAdapter\Search\Hit;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\Hit
 * @uses   \ElasticAdapter\Documents\Document
 * @uses   \ElasticAdapter\Search\Highlight
 */
final class HitTest extends TestCase
{
    public function test_document_can_be_retrieved(): void
    {
        $hit = new Hit([
            '_id' => '1',
            '_source' => ['title' => 'foo']
        ]);

        $this->assertEquals(
            new Document('1', ['title' => 'foo']),
            $hit->getDocument()
        );
    }

    public function test_highlight_can_be_retrieved_if_present(): void
    {
        $hit = new Hit([
            '_id' => '1',
            'highlight' => ['foo' => ['test fragment']]
        ]);

        $this->assertEquals(
            new Highlight(['foo' => ['test fragment']]),
            $hit->getHighlight()
        );
    }

    public function test_nothing_is_returned_when_trying_to_retrieve_highlight_but_it_is_not_present(): void
    {
        $hit = new Hit(['_id' => '1']);

        $this->assertNull($hit->getHighlight());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $hit = new Hit([
            '_id' => '1',
            '_source' => ['title' => 'foo'],
            'highlight' => ['title' => [' <em>foo</em> ']]
        ]);

        $this->assertSame([
            '_id' => '1',
            '_source' => ['title' => 'foo'],
            'highlight' => ['title' => [' <em>foo</em> ']]
        ], $hit->getRaw());
    }
}

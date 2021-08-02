<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\Highlight;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\Highlight
 */
final class HighlightTest extends TestCase
{
    public function test_snippets_can_be_retrieved_for_highlighted_field(): void
    {
        $highlight = new Highlight([
            'message' => [
                ' with the <em>number</em>',
                '  <em>1</em>',
            ],
        ]);

        $this->assertEquals(collect([
            ' with the <em>number</em>',
            '  <em>1</em>',
        ]), $highlight->snippets('message'));
    }

    public function test_empty_collection_is_returned_when_trying_to_retrieve_snippets_for_non_existing_field(): void
    {
        $highlight = new Highlight([
            'foo' => [
                'test fragment',
            ],
        ]);

        $this->assertEquals(collect(), $highlight->snippets('bar'));
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $highlight = new Highlight([
            'foo' => [
                'test fragment 1',
            ],
            'bar' => [
                'test fragment 2',
                'test fragment 3',
            ],
        ]);

        $this->assertSame([
            'foo' => [
                'test fragment 1',
            ],
            'bar' => [
                'test fragment 2',
                'test fragment 3',
            ],
        ], $highlight->raw());
    }
}

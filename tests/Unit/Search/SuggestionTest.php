<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Search;

use Elastic\Adapter\Search\Suggestion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Search\Suggestion
 */
final class SuggestionTest extends TestCase
{
    public function test_text_can_be_retrieved(): void
    {
        $suggestion = new Suggestion(['text' => 'foo']);

        $this->assertSame('foo', $suggestion->text());
    }

    public function test_offset_can_be_retrieved(): void
    {
        $suggestion = new Suggestion(['offset' => 0]);

        $this->assertSame(0, $suggestion->offset());
    }

    public function test_length_can_be_retrieved(): void
    {
        $suggestion = new Suggestion(['length' => 5]);

        $this->assertSame(5, $suggestion->length());
    }

    public function test_options_can_be_retrieved(): void
    {
        $suggestion = new Suggestion([
            'options' => [
                [
                    'text' => 'foo',
                    'score' => 0.8,
                    'freq' => 1,
                ],
            ],
        ]);

        $this->assertEquals(collect([
            [
                'text' => 'foo',
                'score' => 0.8,
                'freq' => 1,
            ],
        ]), $suggestion->options());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $suggestion = new Suggestion([
            'text' => 'foo',
            'offset' => 0,
            'length' => 5,
            'options' => [],
        ]);

        $this->assertSame([
            'text' => 'foo',
            'offset' => 0,
            'length' => 5,
            'options' => [],
        ], $suggestion->raw());
    }
}

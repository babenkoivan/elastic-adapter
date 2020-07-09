<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\Suggestion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\Suggestion
 */
final class SuggestionTest extends TestCase
{
    public function test_text_can_be_retrieved(): void
    {
        $suggestion = new Suggestion(['text' => 'foo']);

        $this->assertSame('foo', $suggestion->getText());
    }

    public function test_offset_can_be_retrieved(): void
    {
        $suggestion = new Suggestion(['offset' => 0]);

        $this->assertSame(0, $suggestion->getOffset());
    }

    public function test_length_can_be_retrieved(): void
    {
        $suggestion = new Suggestion(['length' => 5]);

        $this->assertSame(5, $suggestion->getLength());
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

        $this->assertSame([
            [
                'text' => 'foo',
                'score' => 0.8,
                'freq' => 1,
            ],
        ], $suggestion->getOptions());
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
        ], $suggestion->getRaw());
    }
}

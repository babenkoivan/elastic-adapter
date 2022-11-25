<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Search;

use Elastic\Adapter\Search\Explanation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Search\Explanation
 */
final class ExplanationTest extends TestCase
{
    private Explanation $explanation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->explanation = new Explanation([
            'value' => 1.6943598,
            'description' => 'weight(message:elasticsearch in 0) [PerFieldSimilarity], result of:',
            'details' => [
                [
                    'value' => 1.3862944,
                    'description' => 'score(freq=1.0), computed as boost * idf * tf from:',
                    'details' => [],
                ],
            ],
        ]);
    }

    public function test_value_can_be_retrieved(): void
    {
        $this->assertSame(1.6943598, $this->explanation->value());
    }

    public function test_description_can_be_retrieved(): void
    {
        $this->assertSame(
            'weight(message:elasticsearch in 0) [PerFieldSimilarity], result of:',
            $this->explanation->description()
        );
    }

    public function test_details_can_be_retrieved(): void
    {
        $this->assertCount(1, $this->explanation->details());
        $this->assertSame(1.3862944, $this->explanation->details()->first()->value());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $this->assertSame([
            'value' => 1.6943598,
            'description' => 'weight(message:elasticsearch in 0) [PerFieldSimilarity], result of:',
            'details' => [
                [
                    'value' => 1.3862944,
                    'description' => 'score(freq=1.0), computed as boost * idf * tf from:',
                    'details' => [],
                ],
            ],
        ], $this->explanation->raw());
    }
}

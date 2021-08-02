<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Search;

use ElasticAdapter\Search\Bucket;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Search\Bucket
 */
final class BucketTest extends TestCase
{
    /**
     * @var Bucket
     */
    private $bucket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bucket = new Bucket([
            'key' => 'electronic',
            'doc_count' => 6,
        ]);
    }

    public function test_key_can_be_retrieved(): void
    {
        $this->assertSame('electronic', $this->bucket->key());
    }

    public function test_doc_count_can_be_retrieved(): void
    {
        $this->assertSame(6, $this->bucket->docCount());
    }

    public function test_raw_representation_can_be_retrieved(): void
    {
        $this->assertSame([
            'key' => 'electronic',
            'doc_count' => 6,
        ], $this->bucket->raw());
    }
}

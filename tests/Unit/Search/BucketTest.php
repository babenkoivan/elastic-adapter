<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Search;

use Elastic\Adapter\Search\Bucket;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Search\Bucket
 */
final class BucketTest extends TestCase
{
    private Bucket $bucket;

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

<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Documents;

use ElasticAdapter\Documents\Document;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Documents\Document
 */
final class DocumentTest extends TestCase
{
    public function test_document_getters(): void
    {
        $document = new Document('123456', ['title' => 'foo'], '123');

        $this->assertSame('123456', $document->getId());
        $this->assertSame('123', $document->getRouting());
        $this->assertSame(['title' => 'foo'], $document->getContent());
    }

    public function test_document_routing_nullable(): void
    {
        $document = new Document('123456', ['title' => 'foo']);

        $this->assertNull($document->getRouting());
    }

    public function test_array_casting(): void
    {
        $document = new Document('1', ['title' => 'test']);

        $this->assertSame([
            'id' => '1',
            'content' => ['title' => 'test'],
        ], $document->toArray());
    }
}

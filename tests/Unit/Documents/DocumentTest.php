<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Documents;

use ElasticAdapter\Documents\Document;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Documents\Document
 *
 * @uses   \ElasticAdapter\Support\Arr
 */
final class DocumentTest extends TestCase
{
    public function test_document_getters(): void
    {
        $document = new Document('123456', ['title' => 'book', 'price' => 10]);

        $this->assertSame('123456', $document->getId());
        $this->assertSame(['title' => 'book', 'price' => 10], $document->getContent());
        $this->assertSame('book', $document->getField('title'));
        $this->assertSame(10, $document->getField('price'));
        $this->assertNull($document->getField('not_defined_key'));
        $this->assertNull($document->getField('user.not_defined_key'));
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

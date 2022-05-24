<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Documents;

use Elastic\Adapter\Documents\Document;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Documents\Document
 */
final class DocumentTest extends TestCase
{
    public function test_document_getters(): void
    {
        $document = new Document('123456', ['title' => 'book', 'price' => 10]);

        $this->assertSame('123456', $document->id());
        $this->assertSame(['title' => 'book', 'price' => 10], $document->content());
        $this->assertSame('book', $document->content('title'));
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

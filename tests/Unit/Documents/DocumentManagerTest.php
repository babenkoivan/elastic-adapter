<?php
declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Documents;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Documents\DocumentManager;
use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Documents\DocumentManager
 * @uses   \ElasticAdapter\Documents\Document
 */
final class DocumentManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $client;
    /**
     * @var DocumentManager
     */
    private $documentManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);
        $this->documentManager = new DocumentManager($this->client);
    }

    public function test_documents_can_be_indexed_with_refresh(): void
    {
        $this->client
            ->expects($this->once())
            ->method('bulk')
            ->with([
                'index' => 'test',
                'refresh' => 'true',
                'body' => [
                    ['index' => ['_id' => '1']],
                    ['title' => 'Doc 1'],
                    ['index' => ['_id' => '2']],
                    ['title' => 'Doc 2'],
                ]
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->index('test', [
            new Document('1', ['title' => 'Doc 1']),
            new Document('2', ['title' => 'Doc 2'])
        ], true));
    }

    public function test_documents_can_be_indexed_without_refresh(): void
    {
        $this->client
            ->expects($this->once())
            ->method('bulk')
            ->with([
                'index' => 'test',
                'refresh' => 'false',
                'body' => [
                    ['index' => ['_id' => '1']],
                    ['title' => 'Doc 1'],
                ]
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->index('test', [
            new Document('1', ['title' => 'Doc 1']),
        ], false));
    }

    public function test_documents_can_be_deleted_with_refresh(): void
    {
        $this->client
            ->expects($this->once())
            ->method('bulk')
            ->with([
                'index' => 'test',
                'refresh' => 'true',
                'body' => [
                    ['delete' => ['_id' => '1']],
                    ['delete' => ['_id' => '2']],
                ]
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->delete('test', [
            new Document('1', ['title' => 'Doc 1']),
            new Document('2', ['title' => 'Doc 2'])
        ], true));
    }

    public function test_documents_can_be_deleted_without_refresh(): void
    {
        $this->client
            ->expects($this->once())
            ->method('bulk')
            ->with([
                'index' => 'test',
                'refresh' => 'false',
                'body' => [
                    ['delete' => ['_id' => '1']],
                ]
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->delete('test', [
            new Document('1', ['title' => 'Doc 1']),
        ], false));
    }
}

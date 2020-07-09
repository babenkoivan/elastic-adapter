<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Documents;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticAdapter\Documents\DocumentManager
 *
 * @uses   \ElasticAdapter\Documents\Document
 * @uses   \ElasticAdapter\Search\Hit
 * @uses   \ElasticAdapter\Search\SearchRequest
 * @uses   \ElasticAdapter\Search\SearchResponse
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
                ],
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->index('test', [
            new Document('1', ['title' => 'Doc 1']),
            new Document('2', ['title' => 'Doc 2']),
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
                ],
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
                ],
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->delete('test', [
            new Document('1', ['title' => 'Doc 1']),
            new Document('2', ['title' => 'Doc 2']),
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
                ],
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->delete('test', [
            new Document('1', ['title' => 'Doc 1']),
        ], false));
    }

    public function test_documents_can_be_deleted_by_query_with_refresh(): void
    {
        $this->client
            ->expects($this->once())
            ->method('deleteByQuery')
            ->with([
                'index' => 'test',
                'refresh' => 'true',
                'body' => [
                    'query' => ['match_all' => new stdClass()],
                ],
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->deleteByQuery('test', [
            'match_all' => new stdClass(),
        ], true));
    }

    public function test_documents_can_be_deleted_by_query_without_refresh(): void
    {
        $this->client
            ->expects($this->once())
            ->method('deleteByQuery')
            ->with([
                'index' => 'test',
                'refresh' => 'false',
                'body' => [
                    'query' => ['match_all' => new stdClass()],
                ],
            ]);

        $this->assertSame($this->documentManager, $this->documentManager->deleteByQuery('test', [
            'match_all' => new stdClass(),
        ], false));
    }

    public function test_documents_can_be_found(): void
    {
        $this->client
            ->expects($this->once())
            ->method('search')
            ->with([
                'index' => 'test',
                'body' => [
                    'query' => [
                        'match' => ['content' => 'foo'],
                    ],
                ],
            ])
            ->willReturn([
                'hits' => [
                    'total' => [
                        'value' => 1,
                        'relation' => 'eq',
                    ],
                    'max_score' => 1.601195,
                    'hits' => [
                        [
                            '_index' => 'test',
                            '_id' => '1',
                            '_score' => 1.601195,
                            '_source' => ['content' => 'foo'],
                        ],
                    ],
                ],
            ]);

        $response = $this->documentManager->search('test', new SearchRequest([
            'match' => ['content' => 'foo'],
        ]));

        $this->assertInstanceOf(SearchResponse::class, $response);
        $this->assertSame(1, $response->getHitsTotal());
        $this->assertEquals(new Document('1', ['content' => 'foo']), $response->getHits()[0]->getDocument());
    }
}

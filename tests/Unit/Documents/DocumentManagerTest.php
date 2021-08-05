<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Documents;

use ElasticAdapter\Documents\Document;
use ElasticAdapter\Documents\DocumentManager;
use ElasticAdapter\Documents\Routing;
use ElasticAdapter\Exceptions\BulkRequestException;
use ElasticAdapter\Search\SearchRequest;
use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \ElasticAdapter\Documents\DocumentManager
 *
 * @uses   \ElasticAdapter\Documents\Document
 * @uses   \ElasticAdapter\Documents\Routing
 * @uses   \ElasticAdapter\Exceptions\BulkRequestException
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
            ])
            ->willReturn([
                'took' => 0,
                'errors' => false,
                'items' => [],
            ]);

        $documents = collect([
            new Document('1', ['title' => 'Doc 1']),
            new Document('2', ['title' => 'Doc 2']),
        ]);

        $this->assertSame($this->documentManager, $this->documentManager->index('test', $documents, true));
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
            ])
            ->willReturn([
                'took' => 0,
                'errors' => false,
                'items' => [],
            ]);

        $documents = collect([
            new Document('1', ['title' => 'Doc 1']),
        ]);

        $this->assertSame($this->documentManager, $this->documentManager->index('test', $documents, false));
    }

    public function test_documents_can_be_indexed_with_custom_routing(): void
    {
        $this->client
            ->expects($this->once())
            ->method('bulk')
            ->with([
                'index' => 'test',
                'refresh' => 'true',
                'body' => [
                    ['index' => ['_id' => '1', 'routing' => 'Doc1']],
                    ['title' => 'Doc 1'],
                    ['index' => ['_id' => '2', 'routing' => 'Doc2']],
                    ['title' => 'Doc 2'],
                ],
            ])
            ->willReturn([
                'took' => 0,
                'errors' => false,
                'items' => [],
            ]);

        $documents = collect([
            new Document('1', ['title' => 'Doc 1']),
            new Document('2', ['title' => 'Doc 2']),
        ]);

        $routing = (new Routing())
            ->add('1', 'Doc1')
            ->add('2', 'Doc2');

        $this->assertSame($this->documentManager, $this->documentManager->index('test', $documents, true, $routing));
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
            ])
            ->willReturn([
                'took' => 0,
                'errors' => false,
                'items' => [],
            ]);

        $documentIds = ['1', '2'];

        $this->assertSame($this->documentManager, $this->documentManager->delete('test', $documentIds, true));
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
            ])
            ->willReturn([
                'took' => 0,
                'errors' => false,
                'items' => [],
            ]);

        $documentIds = ['1'];

        $this->assertSame($this->documentManager, $this->documentManager->delete('test', $documentIds, false));
    }

    public function test_documents_can_be_deleted_with_custom_routing(): void
    {
        $this->client
            ->expects($this->once())
            ->method('bulk')
            ->with([
                'index' => 'test',
                'refresh' => 'true',
                'body' => [
                    ['delete' => ['_id' => '1', 'routing' => 'Doc1']],
                    ['delete' => ['_id' => '2', 'routing' => 'Doc2']],
                ],
            ])
            ->willReturn([
                'took' => 0,
                'errors' => false,
                'items' => [],
            ]);

        $documentIds = ['1', '2'];

        $routing = (new Routing())
            ->add('1', 'Doc1')
            ->add('2', 'Doc2');

        $this->assertSame($this->documentManager, $this->documentManager->delete('test', $documentIds, true, $routing));
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

        $query = [
            'match_all' => new stdClass(),
        ];

        $this->assertSame($this->documentManager, $this->documentManager->deleteByQuery('test', $query, true));
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

        $query = [
            'match_all' => new stdClass(),
        ];

        $this->assertSame($this->documentManager, $this->documentManager->deleteByQuery('test', $query, false));
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

        $this->assertSame(1, $response->total());
        $this->assertEquals(new Document('1', ['content' => 'foo']), $response->hits()[0]->document());
    }

    public function test_exception_is_thrown_when_index_operation_was_unsuccessful(): void
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
            ])
            ->willReturn([
                'took' => 0,
                'errors' => true,
                'items' => [],
            ]);

        $this->expectException(BulkRequestException::class);

        $documents = collect([
            new Document('1', ['title' => 'Doc 1']),
        ]);

        $this->documentManager->index('test', $documents);
    }
}

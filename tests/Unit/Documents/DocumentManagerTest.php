<?php declare(strict_types=1);

namespace OpenSearch\Adapter\Tests\Unit\Documents;

use OpenSearch\Adapter\Documents\Document;
use OpenSearch\Adapter\Documents\DocumentManager;
use OpenSearch\Adapter\Documents\Routing;
use OpenSearch\Adapter\Exceptions\BulkOperationException;
use OpenSearch\Adapter\Search\Hit;
use OpenSearch\Adapter\Search\SearchParameters;
use OpenSearch\Client;
use OpenSearch\Laravel\Client\ClientBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \OpenSearch\Adapter\Documents\DocumentManager
 *
 * @uses   \OpenSearch\Adapter\Documents\Document
 * @uses   \OpenSearch\Adapter\Documents\Routing
 * @uses   \OpenSearch\Adapter\Exceptions\BulkOperationException
 * @uses   \OpenSearch\Adapter\Search\Hit
 * @uses   \OpenSearch\Adapter\Search\SearchParameters
 * @uses   \OpenSearch\Adapter\Search\SearchResult
 */
final class DocumentManagerTest extends TestCase
{
    private MockObject $client;
    private DocumentManager $documentManager;

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($this->client);

        $this->documentManager = new DocumentManager($clientBuilder);
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
            ->willReturn([]);

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
            ->willReturn([]);

        $documents = collect([
            new Document('1', ['title' => 'Doc 1']),
        ]);

        $this->assertSame($this->documentManager, $this->documentManager->index('test', $documents));
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
            ->willReturn([]);

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
            ->willReturn([]);

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
            ->willReturn([]);

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
            ->willReturn([]);

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

        $query = ['match_all' => new stdClass()];

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

        $query = ['match_all' => new stdClass()];

        $this->assertSame($this->documentManager, $this->documentManager->deleteByQuery('test', $query));
    }

    public function test_documents_can_be_found(): void
    {
        $response = [
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
        ];

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
            ->willReturn($response);

        $searchParameters = (new SearchParameters())
            ->indices(['test'])
            ->query([
                'match' => [
                    'content' => 'foo',
                ],
            ]);

        $searchResult = $this->documentManager->search($searchParameters);
        $this->assertSame(1, $searchResult->total());

        /** @var Hit $firstHit */
        $firstHit = $searchResult->hits()[0];
        $this->assertEquals(new Document('1', ['content' => 'foo']), $firstHit->document());
    }

    public function test_exception_is_thrown_when_index_operation_was_unsuccessful(): void
    {
        $response = [
            'took' => 0,
            'errors' => true,
            'items' => [],
        ];

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
            ->willReturn($response);

        $this->expectException(BulkOperationException::class);

        $documents = collect([
            new Document('1', ['title' => 'Doc 1']),
        ]);

        $this->documentManager->index('test', $documents);
    }

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    public function test_connection_can_be_changed(): void
    {
        $defaultClient = $this->createMock(Client::class);

        $defaultClient
            ->expects($this->never())
            ->method('bulk');

        $testClient = $this->createMock(Client::class);

        $testClient
            ->expects($this->once())
            ->method('bulk')
            ->with([
                'index' => 'docs',
                'refresh' => 'false',
                'body' => [
                    ['index' => ['_id' => '1']],
                    ['title' => 'Doc 1'],
                ],
            ])
            ->willReturn([]);

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($defaultClient);
        $clientBuilder->method('connection')->with('test')->willReturn($testClient);

        (new DocumentManager($clientBuilder))
            ->connection('test')
            ->index('docs', collect([new Document('1', ['title' => 'Doc 1'])]));
    }
}

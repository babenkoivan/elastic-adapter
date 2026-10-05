<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Documents;

use Elastic\Adapter\Documents\Document;
use Elastic\Adapter\Documents\DocumentManager;
use Elastic\Adapter\Documents\Routing;
use Elastic\Adapter\Exceptions\BulkOperationException;
use Elastic\Adapter\Exceptions\SearchOperationException;
use Elastic\Adapter\Search\Hit;
use Elastic\Adapter\Search\SearchParameters;
use Elastic\Adapter\Search\SearchResult;
use Elastic\Client\ClientBuilderInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(DocumentManager::class)]
#[UsesClass(Document::class)]
#[UsesClass(Routing::class)]
#[UsesClass(BulkOperationException::class)]
#[UsesClass(SearchOperationException::class)]
#[UsesClass(Hit::class)]
#[UsesClass(SearchParameters::class)]
#[UsesClass(SearchResult::class)]
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
        $this->client->method('setAsync')->willReturnSelf();

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
            ->willReturn(
                $this->createMock(Elasticsearch::class)
            );

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
            ->willReturn(
                $this->createMock(Elasticsearch::class)
            );

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
            ->willReturn(
                $this->createMock(Elasticsearch::class)
            );

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
            ->willReturn(
                $this->createMock(Elasticsearch::class)
            );

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
            ->willReturn(
                $this->createMock(Elasticsearch::class)
            );

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
            ->willReturn(
                $this->createMock(Elasticsearch::class)
            );

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
        $response = $this->createMock(Elasticsearch::class);

        $response
            ->expects($this->once())
            ->method('asArray')
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

    public function test_many_documents_can_be_found(): void
    {
        $response = $this->createMock(Elasticsearch::class);

        $response
            ->expects($this->once())
            ->method('asArray')
            ->willReturn([
                'responses' => [
                    [
                        'hits' => [
                            'total' => ['value' => 1, 'relation' => 'eq'],
                            'max_score' => 1.601195,
                            'hits' => [
                                [
                                    '_index' => 'test1',
                                    '_id' => '1',
                                    '_score' => 1.601195,
                                    '_source' => ['content' => 'foo'],
                                ],
                            ],
                        ],
                    ],
                    [
                        'hits' => [
                            'total' => ['value' => 1, 'relation' => 'eq'],
                            'max_score' => 1.0,
                            'hits' => [
                                [
                                    '_index' => 'test2',
                                    '_id' => '2',
                                    '_score' => 1.0,
                                    '_source' => ['content' => 'bar'],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $this->client
            ->expects($this->once())
            ->method('msearch')
            ->with([
                'body' => [
                    ['index' => 'test1'],
                    ['query' => ['match' => ['content' => 'foo']]],
                    ['index' => 'test2'],
                    ['query' => ['match' => ['content' => 'bar']]],
                ],
            ])
            ->willReturn($response);

        $searchParametersOne = (new SearchParameters())
            ->indices(['test1'])
            ->query(['match' => ['content' => 'foo']]);

        $searchParametersTwo = (new SearchParameters())
            ->indices(['test2'])
            ->query(['match' => ['content' => 'bar']]);

        $searchResults = $this->documentManager->searchMany([$searchParametersOne, $searchParametersTwo]);

        $this->assertCount(2, $searchResults);
        $this->assertSame(1, $searchResults->get(0)->total());
        $this->assertSame(1, $searchResults->get(1)->total());

        /** @var Hit $firstHit */
        $firstHit = $searchResults->get(0)->hits()[0];
        $this->assertEquals(new Document('1', ['content' => 'foo']), $firstHit->document());
    }

    public function test_terminate_after_is_moved_into_search_body_for_many_search(): void
    {
        $response = $this->createMock(Elasticsearch::class);

        $response->method('asArray')->willReturn([
            'responses' => [
                [
                    'hits' => ['total' => ['value' => 0, 'relation' => 'eq'], 'max_score' => null, 'hits' => []],
                ],
            ],
        ]);

        $this->client
            ->expects($this->once())
            ->method('msearch')
            ->with([
                'body' => [
                    ['index' => 'test'],
                    ['query' => ['match_all' => new stdClass()], 'terminate_after' => 10],
                ],
            ])
            ->willReturn($response);

        $searchParameters = (new SearchParameters())
            ->indices(['test'])
            ->query(['match_all' => new stdClass()])
            ->terminateAfter(10);

        $this->documentManager->searchMany([$searchParameters]);
    }

    public function test_empty_search_body_is_serialized_as_an_object_for_many_search(): void
    {
        $response = $this->createMock(Elasticsearch::class);

        $response->method('asArray')->willReturn([
            'responses' => [
                [
                    'hits' => ['total' => ['value' => 0, 'relation' => 'eq'], 'max_score' => null, 'hits' => []],
                ],
            ],
        ]);

        $this->client
            ->expects($this->once())
            ->method('msearch')
            ->with([
                'body' => [
                    ['index' => 'test'],
                    new stdClass(),
                ],
            ])
            ->willReturn($response);

        // only a header-level setter is called, so toArray() carries no 'body' key at all
        $searchParameters = (new SearchParameters())->indices(['test']);

        $this->documentManager->searchMany([$searchParameters]);
    }

    public function test_empty_search_header_is_serialized_as_an_object_for_many_search(): void
    {
        $response = $this->createMock(Elasticsearch::class);

        $response->method('asArray')->willReturn([
            'responses' => [
                [
                    'hits' => ['total' => ['value' => 0, 'relation' => 'eq'], 'max_score' => null, 'hits' => []],
                ],
            ],
        ]);

        $this->client
            ->expects($this->once())
            ->method('msearch')
            ->with([
                'body' => [
                    new stdClass(),
                    ['query' => ['match_all' => new stdClass()]],
                ],
            ])
            ->willReturn($response);

        // only a body-level setter is called, so toArray() carries no header key at all
        $searchParameters = (new SearchParameters())->query(['match_all' => new stdClass()]);

        $this->documentManager->searchMany([$searchParameters]);
    }

    public function test_exception_is_thrown_when_one_of_the_searches_failed(): void
    {
        $response = $this->createMock(Elasticsearch::class);

        $response->method('asArray')->willReturn([
            'responses' => [
                [
                    'hits' => [
                        'total' => ['value' => 1, 'relation' => 'eq'],
                        'max_score' => 1.0,
                        'hits' => [
                            [
                                '_index' => 'test1',
                                '_id' => '1',
                                '_score' => 1.0,
                                '_source' => ['content' => 'foo'],
                            ],
                        ],
                    ],
                ],
                [
                    'error' => [
                        'type' => 'index_not_found_exception',
                        'reason' => 'no such index [missing]',
                    ],
                ],
            ],
        ]);

        $this->client->method('msearch')->willReturn($response);

        $this->expectException(SearchOperationException::class);

        $searchParametersOne = (new SearchParameters())
            ->indices(['test1'])
            ->query(['match_all' => new stdClass()]);

        $searchParametersTwo = (new SearchParameters())
            ->indices(['missing'])
            ->query(['match_all' => new stdClass()]);

        $this->documentManager->searchMany([$searchParametersOne, $searchParametersTwo]);
    }

    public function test_exception_is_thrown_when_index_operation_was_unsuccessful(): void
    {
        $response = $this->createMock(Elasticsearch::class);

        $response
            ->expects($this->once())
            ->method('asArray')
            ->willReturn([
                'took' => 0,
                'errors' => true,
                'items' => [],
            ]);

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
        $defaultClient->method('setAsync')->willReturnSelf();

        $defaultClient
            ->expects($this->never())
            ->method('bulk');

        $testClient = $this->createMock(Client::class);
        $testClient->method('setAsync')->willReturnSelf();

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
            ->willReturn(
                $this->createMock(Elasticsearch::class)
            );

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($defaultClient);
        $clientBuilder->method('connection')->with('test')->willReturn($testClient);

        (new DocumentManager($clientBuilder))
            ->connection('test')
            ->index('docs', collect([new Document('1', ['title' => 'Doc 1'])]));
    }
}

<?php
declare(strict_types=1);

namespace ElasticAdaptor\Tests\Unit\Indices;

use ElasticAdaptor\Indices\Index;
use ElasticAdaptor\Indices\IndexManager;
use ElasticAdaptor\Indices\Mapping;
use ElasticAdaptor\Indices\Settings;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdaptor\Indices\IndexManager
 * @uses   \ElasticAdaptor\Indices\Index
 * @uses   \ElasticAdaptor\Indices\Settings
 * @uses   \ElasticAdaptor\Indices\Mapping
 * @uses   \ElasticAdaptor\Support\Str
 */
class IndexManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $client;

    /**
     * @before
     */
    public function makeClient(): void
    {
        $this->client = $this->createMock(Client::class);

        $this->client
            ->method('indices')
            ->willReturn($this->createMock(IndicesNamespace::class));
    }

    public function test_index_can_be_opened(): void
    {
        $indexName = 'foo';

        $this->client->indices()
            ->expects($this->once())
            ->method('open')
            ->with([
                'index' => $indexName
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->open($indexName));
    }

    public function test_index_can_be_closed(): void
    {
        $indexName = 'foo';

        $this->client->indices()
            ->expects($this->once())
            ->method('close')
            ->with([
                'index' => $indexName
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->close($indexName));
    }

    public function test_index_existence_can_be_checked(): void
    {
        $indexName = 'foo';

        $this->client->indices()
            ->expects($this->once())
            ->method('exists')
            ->with([
                'index' => $indexName
            ])
            ->willReturn(true);

        $indexManager = new IndexManager($this->client);

        $this->assertTrue($indexManager->exists($indexName));
    }

    public function test_index_can_be_created_without_mapping_and_settings(): void
    {
        $index = new Index('foo');

        $this->client->indices()
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->getName()
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->create($index));
    }

    public function test_index_can_be_created_without_mapping(): void
    {
        $settings = (new Settings())->numberOfReplicas(2);
        $index = new Index('foo', null, $settings);

        $this->client->indices()
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->getName(),
                'body' => [
                    'settings' => [
                        'number_of_replicas' => 2
                    ]
                ]
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->create($index));
    }

    public function test_index_can_be_created_without_settings(): void
    {
        $mapping = (new Mapping())->text('foo');
        $index = new Index('bar', $mapping);

        $this->client->indices()
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->getName(),
                'body' => [
                    'mappings' => [
                        'properties' => [
                            'foo' => [
                                'type' => 'text'
                            ]
                        ]
                    ]
                ]
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->create($index));
    }

    public function test_mapping_can_be_updated(): void
    {
        $indexName = 'foo';
        $mapping = (new Mapping())->text('bar');

        $this->client->indices()
            ->expects($this->once())
            ->method('putMapping')
            ->with([
                'index' => $indexName,
                'body' => [
                    'properties' => [
                        'bar' => [
                            'type' => 'text'
                        ]
                    ]
                ]
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->putMapping($indexName, $mapping));
    }

    public function test_settings_can_be_updated(): void
    {
        $indexName = 'foo';
        $settings = (new Settings())->numberOfReplicas(2);

        $this->client->indices()
            ->expects($this->once())
            ->method('putSettings')
            ->with([
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'number_of_replicas' => 2
                    ]
                ]
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->putSettings($indexName, $settings));
    }
}

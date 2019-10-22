<?php
declare(strict_types=1);

namespace ElasticAdaptor\Tests\Unit\Indices;

use ElasticAdaptor\Indices\Index;
use ElasticAdaptor\Indices\IndexManager;
use ElasticAdaptor\Indices\Mapping;
use ElasticAdaptor\Indices\Settings;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use InvalidArgumentException;
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
        $index = new Index('foo');

        $this->client->indices()
            ->expects($this->once())
            ->method('open')
            ->with([
                'index' => $index->getName()
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->open($index));
    }

    public function test_index_can_be_closed(): void
    {
        $index = new Index('foo');

        $this->client->indices()
            ->expects($this->once())
            ->method('close')
            ->with([
                'index' => $index->getName()
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->close($index));
    }

    public function test_index_existence_can_be_checked(): void
    {
        $index = new Index('foo');

        $this->client->indices()
            ->expects($this->once())
            ->method('exists')
            ->with([
                'index' => $index->getName()
            ])
            ->willReturn(true);

        $indexManager = new IndexManager($this->client);

        $this->assertTrue($indexManager->exists($index));
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
        $mapping = (new Mapping())->text('foo');
        $index = new Index('bar', $mapping);

        $this->client->indices()
            ->expects($this->once())
            ->method('putMapping')
            ->with([
                'index' => $index->getName(),
                'body' => [
                    'properties' => [
                        'foo' => [
                            'type' => 'text'
                        ]
                    ]
                ]
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->putMapping($index));
    }

    public function test_mapping_update_throws_an_error_if_mapping_is_not_defined(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $index = new Index('foo');
        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->putMapping($index));
    }

    public function test_settings_can_be_updated(): void
    {
        $settings = (new Settings())->numberOfReplicas(2);
        $index = new Index('foo', null, $settings);

        $this->client->indices()
            ->expects($this->once())
            ->method('putSettings')
            ->with([
                'index' => $index->getName(),
                'body' => [
                    'settings' => [
                        'number_of_replicas' => 2
                    ]
                ]
            ]);

        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->putSettings($index));
    }

    public function test_settings_update_throws_an_error_if_settings_are_not_defined(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $index = new Index('foo');
        $indexManager = new IndexManager($this->client);

        $this->assertSame($indexManager, $indexManager->putSettings($index));
    }
}

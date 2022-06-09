<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Indices;

use Elastic\Adapter\Indices\Alias;
use Elastic\Adapter\Indices\Index;
use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Client\ClientBuilderInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Endpoints\Indices;
use Elastic\Elasticsearch\Response\Elasticsearch;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Indices\IndexManager
 *
 * @uses   \Elastic\Adapter\Indices\Alias
 * @uses   \Elastic\Adapter\Indices\Index
 * @uses   \Elastic\Adapter\Indices\Mapping
 * @uses   \Elastic\Adapter\Indices\MappingProperties
 * @uses   \Elastic\Adapter\Indices\Settings
 */
class IndexManagerTest extends TestCase
{
    private MockObject $indices;
    private IndexManager $indexManager;

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->indices = $this->createMock(Indices::class);

        $client = $this->createMock(Client::class);
        $client->method('setAsync')->willReturnSelf();
        $client->method('indices')->willReturn($this->indices);

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($client);

        $this->indexManager = new IndexManager($clientBuilder);
    }

    public function test_index_can_be_opened(): void
    {
        $indexName = 'foo';

        $this->indices
            ->expects($this->once())
            ->method('open')
            ->with([
                'index' => $indexName,
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->open($indexName));
    }

    public function test_index_can_be_closed(): void
    {
        $indexName = 'foo';

        $this->indices
            ->expects($this->once())
            ->method('close')
            ->with([
                'index' => $indexName,
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->close($indexName));
    }

    public function test_index_existence_can_be_checked(): void
    {
        $indexName = 'foo';

        $response = $this->createMock(Elasticsearch::class);
        $response->method('asBool')->willReturn(true);

        $this->indices
            ->expects($this->once())
            ->method('exists')
            ->with([
                'index' => $indexName,
            ])
            ->willReturn($response);

        $this->assertTrue($this->indexManager->exists($indexName));
    }

    public function test_index_can_be_created_without_mapping_and_settings(): void
    {
        $index = new Index('foo');

        $this->indices
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->name(),
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->create($index));
    }

    public function test_index_can_be_created_without_mapping(): void
    {
        $settings = (new Settings())->index(['number_of_replicas' => 2]);
        $index = new Index('foo', null, $settings);

        $this->indices
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->name(),
                'body' => [
                    'settings' => [
                        'index' => [
                            'number_of_replicas' => 2,
                        ],
                    ],
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->create($index));
    }

    public function test_index_can_be_created_without_settings(): void
    {
        $mapping = (new Mapping())->text('foo');
        $index = new Index('bar', $mapping);

        $this->indices
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->name(),
                'body' => [
                    'mappings' => [
                        'properties' => [
                            'foo' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->create($index));
    }

    public function test_index_can_be_created_with_empty_settings_and_mapping(): void
    {
        $index = new Index('foo', new Mapping(), new Settings());

        $this->indices
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->name(),
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->create($index));
    }

    public function test_index_can_be_created_with_raw_mapping_and_settings(): void
    {
        $indexName = 'foo';
        $mapping = ['properties' => ['bar' => ['type' => 'text']]];
        $settings = ['index' => ['number_of_replicas' => 2]];

        $this->indices
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $indexName,
                'body' => [
                    'mappings' => $mapping,
                    'settings' => $settings,
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->createRaw($indexName, $mapping, $settings));
    }

    public function test_mapping_can_be_updated(): void
    {
        $indexName = 'foo';
        $mapping = (new Mapping())->text('bar');

        $this->indices
            ->expects($this->once())
            ->method('putMapping')
            ->with([
                'index' => $indexName,
                'body' => [
                    'properties' => [
                        'bar' => [
                            'type' => 'text',
                        ],
                    ],
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->putMapping($indexName, $mapping));
    }

    public function test_mapping_can_be_updated_with_raw_data(): void
    {
        $indexName = 'foo';
        $mapping = ['properties' => ['bar' => ['type' => 'text']]];

        $this->indices
            ->expects($this->once())
            ->method('putMapping')
            ->with([
                'index' => $indexName,
                'body' => $mapping,
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->putMappingRaw($indexName, $mapping));
    }

    public function test_settings_can_be_updated(): void
    {
        $indexName = 'foo';
        $settings = (new Settings())->index(['number_of_replicas' => 2]);

        $this->indices
            ->expects($this->once())
            ->method('putSettings')
            ->with([
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'index' => [
                            'number_of_replicas' => 2,
                        ],
                    ],
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->putSettings($indexName, $settings));
    }

    public function test_settings_can_be_updated_with_raw_data(): void
    {
        $indexName = 'foo';
        $settings = ['index' => ['number_of_replicas' => 2]];

        $this->indices
            ->expects($this->once())
            ->method('putSettings')
            ->with([
                'index' => $indexName,
                'body' => [
                    'settings' => $settings,
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->putSettingsRaw($indexName, $settings));
    }

    public function test_index_can_be_dropped(): void
    {
        $indexName = 'foo';

        $this->indices
            ->expects($this->once())
            ->method('delete')
            ->with([
                'index' => $indexName,
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->drop($indexName));
    }

    public function test_alias_can_be_created(): void
    {
        $indexName = 'foo';
        $alias = (new Alias('bar', true, ['term' => ['user_id' => 12]], '12'));

        $this->indices
            ->expects($this->once())
            ->method('putAlias')
            ->with([
                'index' => $indexName,
                'name' => $alias->name(),
                'body' => [
                    'is_write_index' => true,
                    'routing' => '12',
                    'filter' => [
                        'term' => [
                            'user_id' => 12,
                        ],
                    ],
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->putAlias($indexName, $alias));
    }

    public function test_alias_can_be_created_with_raw_data(): void
    {
        $indexName = 'foo';
        $aliasName = 'bar';
        $settings = ['routing' => '1'];

        $this->indices
            ->expects($this->once())
            ->method('putAlias')
            ->with([
                'index' => $indexName,
                'name' => $aliasName,
                'body' => [
                    'routing' => '1',
                ],
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->putAliasRaw($indexName, $aliasName, $settings));
    }

    public function test_alias_can_be_deleted(): void
    {
        $indexName = 'foo';
        $aliasName = 'bar';

        $this->indices
            ->expects($this->once())
            ->method('deleteAlias')
            ->with([
                'index' => $indexName,
                'name' => $aliasName,
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->deleteAlias($indexName, $aliasName));
    }

    public function test_aliases_can_be_retrieved(): void
    {
        $indexName = 'foo';
        $aliasName = 'bar';

        $response = $this->createMock(Elasticsearch::class);

        $response
            ->expects($this->once())
            ->method('asArray')
            ->willReturn([
                $indexName => [
                    'aliases' => [
                        $aliasName => [],
                    ],
                ],
            ]);

        $this->indices
            ->expects($this->once())
            ->method('getAlias')
            ->with([
                'index' => $indexName,
            ])
            ->willReturn($response);

        $this->assertEquals(
            collect([$aliasName]),
            $this->indexManager->getAliases($indexName)
        );
    }

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    public function test_connection_can_be_changed(): void
    {
        $defaultIndices = $this->createMock(Indices::class);
        $defaultIndices->expects($this->never())->method('create');

        $defaultClient = $this->createMock(Client::class);
        $defaultClient->method('setAsync')->willReturnSelf();
        $defaultClient->method('indices')->willReturn($defaultIndices);

        $testIndices = $this->createMock(Indices::class);
        $testIndices->expects($this->once())->method('open')->with(['index' => 'docs']);

        $testClient = $this->createMock(Client::class);
        $testClient->method('setAsync')->willReturnSelf();
        $testClient->method('indices')->willReturn($testIndices);

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($defaultClient);
        $clientBuilder->method('connection')->with('test')->willReturn($testClient);

        (new IndexManager($clientBuilder))->connection('test')->open('docs');
    }
}

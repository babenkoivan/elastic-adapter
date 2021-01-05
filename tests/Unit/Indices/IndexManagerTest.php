<?php declare(strict_types=1);

namespace ElasticAdapter\Tests\Unit\Indices;

use ElasticAdapter\Indices\Alias;
use ElasticAdapter\Indices\Index;
use ElasticAdapter\Indices\IndexManager;
use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ElasticAdapter\Indices\IndexManager
 *
 * @uses   \ElasticAdapter\Indices\Alias
 * @uses   \ElasticAdapter\Indices\Index
 * @uses   \ElasticAdapter\Indices\Mapping
 * @uses   \ElasticAdapter\Indices\Settings
 * @uses   \ElasticAdapter\Support\Str
 */
class IndexManagerTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $indices;
    /**
     * @var IndexManager
     */
    private $indexManager;

    protected function setUp(): void
    {
        parent::setUp();

        $client = $this->createMock(Client::class);
        $this->indices = $this->createMock(IndicesNamespace::class);

        $client
            ->method('indices')
            ->willReturn($this->indices);

        $this->indexManager = new IndexManager($client);
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

        $this->indices
            ->expects($this->once())
            ->method('exists')
            ->with([
                'index' => $indexName,
            ])
            ->willReturn(true);

        $this->assertTrue($this->indexManager->exists($indexName));
    }

    public function test_index_can_be_created_without_mapping_and_settings(): void
    {
        $index = new Index('foo');

        $this->indices
            ->expects($this->once())
            ->method('create')
            ->with([
                'index' => $index->getName(),
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
                'index' => $index->getName(),
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
                'index' => $index->getName(),
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
                'index' => $index->getName(),
            ]);

        $this->assertSame($this->indexManager, $this->indexManager->create($index));
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

    public function test_aliases_can_be_retrieved(): void
    {
        $indexName = 'foo';
        $aliasName = 'bar';

        $this->indices
            ->expects($this->once())
            ->method('getAlias')
            ->with([
                'index' => $indexName,
            ])
            ->willReturn([
                $indexName => [
                    'aliases' => [
                        $aliasName => [],
                    ],
                ],
            ]);

        $this->assertEquals([new Alias($aliasName)], $this->indexManager->getAliases($indexName));
    }
}

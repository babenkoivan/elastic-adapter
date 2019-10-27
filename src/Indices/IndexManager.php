<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;

final class IndexManager
{
    /**
     * @var IndicesNamespace
     */
    private $indices;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->indices = $client->indices();
    }

    /**
     * @param string $indexName
     * @return IndexManager
     */
    public function open(string $indexName): self
    {
        $this->indices->open([
            'index' => $indexName
        ]);

        return $this;
    }

    /**
     * @param string $indexName
     * @return IndexManager
     */
    public function close(string $indexName): self
    {
        $this->indices->close([
            'index' => $indexName
        ]);

        return $this;
    }

    /**
     * @param string $indexName
     * @return bool
     */
    public function exists(string $indexName): bool
    {
        return $this->indices->exists([
            'index' => $indexName
        ]);
    }

    /**
     * @param Index $index
     * @return $this
     */
    public function create(Index $index): self
    {
        $params = [
            'index' => $index->getName()
        ];

        if ($mapping = $index->getMapping()) {
            $params['body']['mappings'] = $mapping->toArray();
        }

        if ($settings = $index->getSettings()) {
            $params['body']['settings'] = $settings->toArray();
        }

        $this->indices->create($params);

        return $this;
    }

    /**
     * @param string $indexName
     * @param Mapping $mapping
     * @return IndexManager
     */
    public function putMapping(string $indexName, Mapping $mapping): self
    {
        $this->indices->putMapping([
            'index' => $indexName,
            'body' => $mapping->toArray()
        ]);

        return $this;
    }

    /**
     * @param string $indexName
     * @param Settings $settings
     * @return IndexManager
     */
    public function putSettings(string $indexName, Settings $settings): self
    {
        $this->indices->putSettings([
            'index' => $indexName,
            'body' => [
                'settings' => $settings->toArray()
            ]
        ]);

        return $this;
    }

    /**
     * @param string $indexName
     * @return IndexManager
     */
    public function drop(string $indexName): self
    {
        $this->indices->delete([
            'index' => $indexName
        ]);

        return $this;
    }
}

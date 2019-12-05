<?php
declare(strict_types=1);

namespace ElasticAdapter\Indices;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;

final class IndexManager
{
    /**
     * @var IndicesNamespace
     */
    private $indices;

    public function __construct(Client $client)
    {
        $this->indices = $client->indices();
    }

    public function open(string $indexName): self
    {
        $this->indices->open([
            'index' => $indexName
        ]);

        return $this;
    }

    public function close(string $indexName): self
    {
        $this->indices->close([
            'index' => $indexName
        ]);

        return $this;
    }

    public function exists(string $indexName): bool
    {
        return $this->indices->exists([
            'index' => $indexName
        ]);
    }

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

    public function putMapping(string $indexName, Mapping $mapping): self
    {
        $this->indices->putMapping([
            'index' => $indexName,
            'body' => $mapping->toArray()
        ]);

        return $this;
    }

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

    public function drop(string $indexName): self
    {
        $this->indices->delete([
            'index' => $indexName
        ]);

        return $this;
    }
}

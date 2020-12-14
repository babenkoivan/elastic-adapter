<?php declare(strict_types=1);

namespace ElasticAdapter\Indices;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;

class IndexManager
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
            'index' => $indexName,
        ]);

        return $this;
    }

    public function close(string $indexName): self
    {
        $this->indices->close([
            'index' => $indexName,
        ]);

        return $this;
    }

    public function exists(string $indexName): bool
    {
        return $this->indices->exists([
            'index' => $indexName,
        ]);
    }

    public function create(Index $index): self
    {
        $mapping = $index->getMapping() === null ? [] : $index->getMapping()->toArray();
        $settings = $index->getSettings() === null ? [] : $index->getSettings()->toArray();

        $params = [
            'index' => $index->getName(),
        ];

        if (count($mapping) > 0) {
            $params['body']['mappings'] = $mapping;
        }

        if (count($settings) > 0) {
            $params['body']['settings'] = $settings;
        }

        $this->indices->create($params);

        return $this;
    }

    public function putMapping(string $indexName, Mapping $mapping): self
    {
        $this->indices->putMapping([
            'index' => $indexName,
            'body' => $mapping->toArray(),
        ]);

        return $this;
    }

    public function putSettings(string $indexName, Settings $settings): self
    {
        $this->indices->putSettings([
            'index' => $indexName,
            'body' => [
                'settings' => $settings->toArray(),
            ],
        ]);

        return $this;
    }

    public function drop(string $indexName): self
    {
        $this->indices->delete([
            'index' => $indexName,
        ]);

        return $this;
    }

    /**
     * @return Alias[]
     */
    public function getAliases(string $indexName): array
    {
        $response = $this->indices->getAlias([
            'index' => $indexName,
        ]);

        $aliases = $response[$indexName]['aliases'] ?? [];

        return array_map(static function (array $parameters, string $name) {
            return new Alias(
                $name,
                $parameters['filter'] ?? null,
                $parameters['routing'] ?? null
            );
        }, $aliases, array_keys($aliases));
    }
}

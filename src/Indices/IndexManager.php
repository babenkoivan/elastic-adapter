<?php declare(strict_types=1);

namespace Elastic\Adapter\Indices;

use Elastic\Adapter\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Illuminate\Support\Collection;

class IndexManager
{
    use Client;

    public function open(string $indexName): self
    {
        $this->client->indices()->open([
            'index' => $indexName,
        ]);

        return $this;
    }

    public function close(string $indexName): self
    {
        $this->client->indices()->close([
            'index' => $indexName,
        ]);

        return $this;
    }

    public function exists(string $indexName): bool
    {
        /** @var Elasticsearch $response */
        $response = $this->client->indices()->exists([
            'index' => $indexName,
        ]);

        return $response->asBool();
    }

    public function create(Index $index): self
    {
        $params = [
            'index' => $index->name(),
        ];

        $mapping = $index->mapping() === null ? [] : $index->mapping()->toArray();
        $settings = $index->settings() === null ? [] : $index->settings()->toArray();

        if (!empty($mapping)) {
            $params['body']['mappings'] = $mapping;
        }

        if (!empty($settings)) {
            $params['body']['settings'] = $settings;
        }

        $this->client->indices()->create($params);

        return $this;
    }

    public function createRaw(string $indexName, ?array $mapping = null, ?array $settings = null): self
    {
        $params = [
            'index' => $indexName,
        ];

        if (isset($mapping)) {
            $params['body']['mappings'] = $mapping;
        }

        if (isset($settings)) {
            $params['body']['settings'] = $settings;
        }

        $this->client->indices()->create($params);

        return $this;
    }

    public function putMapping(string $indexName, Mapping $mapping): self
    {
        $this->client->indices()->putMapping([
            'index' => $indexName,
            'body' => $mapping->toArray(),
        ]);

        return $this;
    }

    public function putMappingRaw(string $indexName, array $mapping): self
    {
        $this->client->indices()->putMapping([
            'index' => $indexName,
            'body' => $mapping,
        ]);

        return $this;
    }

    public function putSettings(string $indexName, Settings $settings): self
    {
        $this->client->indices()->putSettings([
            'index' => $indexName,
            'body' => [
                'settings' => $settings->toArray(),
            ],
        ]);

        return $this;
    }

    public function putSettingsRaw(string $indexName, array $settings): self
    {
        $this->client->indices()->putSettings([
            'index' => $indexName,
            'body' => [
                'settings' => $settings,
            ],
        ]);

        return $this;
    }

    public function drop(string $indexName): self
    {
        $this->client->indices()->delete([
            'index' => $indexName,
        ]);

        return $this;
    }

    public function putAlias(string $indexName, Alias $alias): self
    {
        $params = [
            'index' => $indexName,
            'name' => $alias->name(),
        ];

        if ($alias->isWriteIndex()) {
            $params['body']['is_write_index'] = $alias->isWriteIndex();
        }

        if ($alias->routing()) {
            $params['body']['routing'] = $alias->routing();
        }

        if ($alias->filter()) {
            $params['body']['filter'] = $alias->filter();
        }

        $this->client->indices()->putAlias($params);

        return $this;
    }

    public function putAliasRaw(string $indexName, string $aliasName, ?array $settings = null): self
    {
        $params = [
            'index' => $indexName,
            'name' => $aliasName,
        ];

        if (isset($settings)) {
            $params['body'] = $settings;
        }

        $this->client->indices()->putAlias($params);

        return $this;
    }

    public function deleteAlias(string $indexName, string $aliasName): self
    {
        $this->client->indices()->deleteAlias([
            'index' => $indexName,
            'name' => $aliasName,
        ]);

        return $this;
    }

    public function getAliases(string $indexName): Collection
    {
        /** @var Elasticsearch $response */
        $response = $this->client->indices()->getAlias([
            'index' => $indexName,
        ]);

        $rawResult = $response->asArray();

        return collect(array_keys($rawResult[$indexName]['aliases'] ?? []));
    }
}

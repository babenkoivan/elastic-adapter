<?php declare(strict_types=1);

namespace ElasticAdapter\Indices;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use Illuminate\Support\Collection;

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

    public function create(IndexBlueprint $index): self
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

        $this->indices->create($params);

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

    public function putMappingRaw(string $indexName, array $mapping): self
    {
        $this->indices->putMapping([
            'index' => $indexName,
            'body' => $mapping,
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

    public function putSettingsRaw(string $indexName, array $settings): self
    {
        $this->indices->putSettings([
            'index' => $indexName,
            'body' => [
                'settings' => $settings,
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
     * @return Collection|Alias[]
     */
    public function getAliases(string $indexName): Collection
    {
        $response = $this->indices->getAlias([
            'index' => $indexName,
        ]);

        $aliases = $response[$indexName]['aliases'] ?? [];

        return collect($aliases)->map(static function (array $parameters, string $name) {
            return new Alias(
                $name,
                $parameters['filter'] ?? null,
                $parameters['routing'] ?? null
            );
        });
    }

    public function putAlias(string $indexName, Alias $alias): self
    {
        $params = [
            'index' => $indexName,
            'name' => $alias->name(),
        ];

        if ($alias->routing()) {
            $params['body']['routing'] = $alias->routing();
        }

        if ($alias->filter()) {
            $params['body']['filter'] = $alias->filter();
        }

        $this->indices->putAlias($params);

        return $this;
    }

    public function deleteAlias(string $indexName, string $aliasName): self
    {
        $this->indices->deleteAlias([
            'index' => $indexName,
            'name' => $aliasName,
        ]);

        return $this;
    }
}

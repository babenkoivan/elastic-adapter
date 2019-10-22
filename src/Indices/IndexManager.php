<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

use Elasticsearch\Client;
use Elasticsearch\Namespaces\IndicesNamespace;
use InvalidArgumentException;

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
     * @param Index $index
     * @return $this
     */
    public function open(Index $index): self
    {
        $this->indices->open([
            'index' => $index->getName()
        ]);

        return $this;
    }

    /**
     * @param Index $index
     * @return $this
     */
    public function close(Index $index): self
    {
        $this->indices->close([
            'index' => $index->getName()
        ]);

        return $this;
    }

    /**
     * @param Index $index
     * @return bool
     */
    public function exists(Index $index): bool
    {
        return $this->indices->exists([
            'index' => $index->getName()
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
     * @param Index $index
     * @return $this
     */
    public function putMapping(Index $index): self
    {
        if (!$mapping = $index->getMapping()) {
            throw new InvalidArgumentException('Mapping is not provided');
        }

        $this->indices->putMapping([
            'index' => $index->getName(),
            'body' => $mapping->toArray()
        ]);

        return $this;
    }

    /**
     * @param Index $index
     * @return $this
     */
    public function putSettings(Index $index): self
    {
        if (!$settings = $index->getSettings()) {
            throw new InvalidArgumentException('Settings are not provided');
        }

        $this->indices->putSettings([
            'index' => $index->getName(),
            'body' => [
                'settings' => $settings->toArray()
            ]
        ]);

        return $this;
    }

    /**
     * @param Index $index
     * @return $this
     */
    public function drop(Index $index): self
    {
        $this->indices->delete([
            'index' => $index->getName()
        ]);

        return $this;
    }
}

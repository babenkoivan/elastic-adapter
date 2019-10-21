<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

use ElasticAdaptor\Documents\Document;
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

        if ($settings = $index->getSettings()) {
            $params['body']['settings'] = $settings->toArray();
        }

        if ($mapping = $index->getMapping()) {
            $params['body']['mappings'] = [
                Document::getType() => $mapping->toArray()
            ];
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
        $params = [
            'index' => $index->getName(),
            'type' => Document::getType()
        ];

        if ($mapping = $index->getMapping()) {
            $params['body'] = [
                Document::getType() => $mapping->toArray()
            ];
        }

        $this->indices->putMapping($params);

        return $this;
    }

    /**
     * @param Index $index
     * @return $this
     */
    public function putSettings(Index $index): self
    {
        $params = [
            'index' => $index->getName()
        ];

        if ($settings = $index->getSettings()) {
            $params['body']['settings'] = $settings->toArray();
        }

        $this->indices->putSettings($params);

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

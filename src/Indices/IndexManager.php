<?php
declare(strict_types=1);

namespace ElasticAdaptor\Indices;

use Elasticsearch\Client;

final class IndexManager
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Index $index
     */
    public function open(Index $index): void
    {

    }

    /**
     * @param Index $index
     */
    public function close(Index $index): void
    {

    }

    /**
     * @param Index $index
     * @return bool
     */
    public function exists(Index $index): bool
    {

    }

    /**
     * @param Index $index
     */
    public function create(Index $index): void
    {

    }

    /**
     * @param Index $index
     */
    public function updateMapping(Index $index): void
    {

    }

    /**
     * @param Index $index
     */
    public function updateSettings(Index $index): void
    {

    }

    /**
     * @param Index $index
     */
    public function drop(Index $index): void
    {

    }
}

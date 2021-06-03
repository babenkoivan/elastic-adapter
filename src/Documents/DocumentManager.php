<?php declare(strict_types=1);

namespace ElasticAdapter\Documents;

use ElasticAdapter\Exceptions\BulkRequestException;
use ElasticAdapter\Search\SearchRequest;
use ElasticAdapter\Search\SearchResponse;
use Elasticsearch\Client;

class DocumentManager
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Document[] $documents
     */
    public function index(
        string $indexName,
        array $documents,
        bool $refresh = false,
        ?string $routingPath = null
    ): self {
        $params = [
            'index' => $indexName,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => [],
        ];

        foreach ($documents as $document) {
            $index = ['_id' => $document->getId()];

            if (isset($routingPath)) {
                $index['routing'] = $document->getField($routingPath);
            }

            $params['body'][] = compact('index');
            $params['body'][] = $document->getContent();
        }

        $response = $this->client->bulk($params);

        if ($response['errors']) {
            throw new BulkRequestException($response);
        }

        return $this;
    }

    /**
     * @param Document[] $documents
     */
    public function delete(
        string $indexName,
        array $documents,
        bool $refresh = false,
        ?string $routingPath = null
    ): self {
        $params = [
            'index' => $indexName,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => [],
        ];

        foreach ($documents as $document) {
            $delete = ['_id' => $document->getId()];

            if (isset($routingPath)) {
                $delete['routing'] = $document->getField($routingPath);
            }

            $params['body'][] = compact('delete');
        }

        $response = $this->client->bulk($params);

        if ($response['errors']) {
            throw new BulkRequestException($response);
        }

        return $this;
    }

    public function deleteByQuery(string $indexName, array $query, bool $refresh = false): self
    {
        $params = [
            'index' => $indexName,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => compact('query'),
        ];

        $this->client->deleteByQuery($params);

        return $this;
    }

    public function search(string $indexName, SearchRequest $request): SearchResponse
    {
        $params = [
            'index' => $indexName,
            'body' => $request->toArray(),
        ];

        $response = $this->client->search($params);

        return new SearchResponse($response);
    }
}

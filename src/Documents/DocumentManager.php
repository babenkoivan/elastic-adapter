<?php declare(strict_types=1);

namespace ElasticAdapter\Documents;

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
    public function index(string $indexName, array $documents, bool $refresh = false): self
    {
        $params = [
            'index' => $indexName,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => [],
        ];

        foreach ($documents as $document) {
            $indexConfig = ['index' => ['_id' => $document->getId()]];
            if (!is_null($document->getRouting())) {
                $indexConfig['index']['_routing'] = $document->getRouting();
            }

            $params['body'][] = $indexConfig;
            $params['body'][] = $document->getContent();
        }

        $this->client->bulk($params);

        return $this;
    }

    /**
     * @param Document[] $documents
     */
    public function delete(string $indexName, array $documents, bool $refresh = false): self
    {
        $params = [
            'index' => $indexName,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => [],
        ];

        foreach ($documents as $document) {
            $deleteConfig = ['delete' => ['_id' => $document->getId()]];
            if (!is_null($document->getRouting())) {
                $deleteConfig['delete']['_routing'] = $document->getRouting();
            }
            $params['body'][] = $deleteConfig;
        }

        $this->client->bulk($params);

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

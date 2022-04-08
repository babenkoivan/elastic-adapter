<?php declare(strict_types=1);

namespace ElasticAdapter\Documents;

use ElasticAdapter\Exceptions\BulkOperationException;
use ElasticAdapter\Search\SearchParameters;
use ElasticAdapter\Search\SearchResponse;
use Elasticsearch\Client;
use Illuminate\Support\Collection;

class DocumentManager
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Collection|Document[] $documents
     */
    public function index(
        string $indexName,
        Collection $documents,
        bool $refresh = false,
        Routing $routing = null
    ): self {
        $params = [
            'index' => $indexName,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => [],
        ];

        foreach ($documents as $document) {
            $index = ['_id' => $document->id()];

            if ($routing && $routing->has($document->id())) {
                $index['routing'] = $routing->get($document->id());
            }

            $params['body'][] = compact('index');
            $params['body'][] = $document->content();
        }

        $response = $this->client->bulk($params);

        if ($response['errors']) {
            throw new BulkOperationException($response);
        }

        return $this;
    }

    /**
     * @param string[] $documentIds
     */
    public function delete(
        string $indexName,
        array $documentIds,
        bool $refresh = false,
        Routing $routing = null
    ): self {
        $params = [
            'index' => $indexName,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => [],
        ];

        foreach ($documentIds as $documentId) {
            $delete = ['_id' => $documentId];

            if ($routing && $routing->has($documentId)) {
                $delete['routing'] = $routing->get($documentId);
            }

            $params['body'][] = compact('delete');
        }

        $response = $this->client->bulk($params);

        if ($response['errors']) {
            throw new BulkOperationException($response);
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

    public function search(string $indexName, SearchParameters $searchParameters): SearchResponse
    {
        $params = $searchParameters->toArray();
        $params['index'] = $indexName;

        $response = $this->client->search($params);
        return new SearchResponse($response);
    }
}

<?php
declare(strict_types=1);

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
     * @param  string  $index
     * @param  Document[]  $documents
     * @param  bool  $refresh
     * @return DocumentManager
     */
    public function index(string $index, array $documents, bool $refresh = false): self
    {
        $params = [
            'index' => $index,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => []
        ];

        foreach ($documents as $document) {
            $params['body'][] = ['index' => ['_id' => $document->getId()]];
            $params['body'][] = $document->getContent();
        }

        $this->client->bulk($params);

        return $this;
    }

    /**
     * @param  string  $index
     * @param  Document[]  $documents
     * @param  bool  $refresh
     * @return DocumentManager
     */
    public function delete(string $index, array $documents, bool $refresh = false): self
    {
        $params = [
            'index' => $index,
            'refresh' => $refresh ? 'true' : 'false',
            'body' => []
        ];

        foreach ($documents as $document) {
            $params['body'][] = ['delete' => ['_id' => $document->getId()]];
        }

        $this->client->bulk($params);

        return $this;
    }

    public function search(string $index, SearchRequest $request): SearchResponse
    {
        $params = [
            'index' => $index,
            'body' => $request->toArray()
        ];

        $response = $this->client->search($params);

        return new SearchResponse($response);
    }
}

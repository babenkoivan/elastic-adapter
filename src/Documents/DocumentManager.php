<?php declare(strict_types=1);

namespace ElasticAdapter\Documents;

use Elastic\Elasticsearch\Response\Elasticsearch;
use ElasticAdapter\Client;
use ElasticAdapter\Exceptions\BulkOperationException;
use ElasticAdapter\Search\SearchParameters;
use ElasticAdapter\Search\SearchResult;
use Illuminate\Support\Collection;

class DocumentManager
{
    use Client;

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

        /** @var Elasticsearch $response */
        $response = $this->client->bulk($params);
        $rawResult = $response->asArray();

        if ($rawResult['errors'] ?? false) {
            throw new BulkOperationException($rawResult);
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

        /** @var Elasticsearch $response */
        $response = $this->client->bulk($params);
        $rawResult = $response->asArray();

        if ($rawResult['errors'] ?? false) {
            throw new BulkOperationException($rawResult);
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

    public function search(string $indexName, SearchParameters $searchParameters): SearchResult
    {
        $params = $searchParameters->toArray();
        $params['index'] = $indexName;

        /** @var Elasticsearch $response */
        $response = $this->client->search($params);
        $rawResult = $response->asArray();

        return new SearchResult($rawResult);
    }
}

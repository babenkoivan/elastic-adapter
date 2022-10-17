<?php declare(strict_types=1);

namespace OpenSearch\Adapter\Documents;

use Illuminate\Support\Collection;
use OpenSearch\Adapter\Client;
use OpenSearch\Adapter\Exceptions\BulkOperationException;
use OpenSearch\Adapter\Search\SearchParameters;
use OpenSearch\Adapter\Search\SearchResult;

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

        $rawResult = $this->client->bulk($params);

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

        $rawResult = $this->client->bulk($params);

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

    public function search(SearchParameters $searchParameters): SearchResult
    {
        $params = $searchParameters->toArray();

        $rawResult = $this->client->search($params);

        return new SearchResult($rawResult);
    }
}

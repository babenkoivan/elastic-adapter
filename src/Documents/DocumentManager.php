<?php declare(strict_types=1);

namespace Elastic\Adapter\Documents;

use Elastic\Adapter\Client;
use Elastic\Adapter\Exceptions\BulkOperationException;
use Elastic\Adapter\Exceptions\SearchOperationException;
use Elastic\Adapter\Search\SearchParameters;
use Elastic\Adapter\Search\SearchResult;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Illuminate\Support\Collection;
use stdClass;

class DocumentManager
{
    use Client;

    /**
     * @param Collection<Document> $documents
     */
    public function index(
        string $indexName,
        Collection $documents,
        bool $refresh = false,
        ?Routing $routing = null
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
        ?Routing $routing = null
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

    public function search(SearchParameters $searchParameters): SearchResult
    {
        $params = $searchParameters->toArray();

        /** @var Elasticsearch $response */
        $response = $this->client->search($params);
        $rawResult = $response->asArray();

        return new SearchResult($rawResult);
    }

    /**
     * @param array<SearchParameters> $searchParameters
     *
     * @return Collection<SearchResult>
     */
    public function searchMany(array $searchParameters): Collection
    {
        $body = [];

        foreach ($searchParameters as $parameters) {
            $params = $parameters->toArray();
            $searchBody = (array) ($params['body'] ?? []);
            unset($params['body']);

            // terminate_after is not a valid _msearch header key, but it is accepted in the search body
            if (isset($params['terminate_after'])) {
                $searchBody['terminate_after'] = $params['terminate_after'];
                unset($params['terminate_after']);
            }

            // an empty array is serialized as [], which cannot be parsed as a header or a search body
            $body[] = $params ?: new stdClass();
            $body[] = $searchBody ?: new stdClass();
        }

        /** @var Elasticsearch $response */
        $response = $this->client->msearch(['body' => $body]);
        $rawResult = $response->asArray();

        return collect($rawResult['responses'] ?? [])->map(static function (array $rawSearchResult) {
            if (isset($rawSearchResult['error'])) {
                throw new SearchOperationException($rawSearchResult);
            }

            return new SearchResult($rawSearchResult);
        });
    }
}

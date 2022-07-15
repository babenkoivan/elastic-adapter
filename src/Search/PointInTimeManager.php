<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use Elastic\Adapter\Client;
use Elastic\Elasticsearch\Response\Elasticsearch;

final class PointInTimeManager
{
    use Client;

    public function open(string $indexName, ?string $keepAlive = null): string
    {
        $params = ['index' => $indexName];

        if (isset($keepAlive)) {
            $params['keep_alive'] = $keepAlive;
        }

        /** @var Elasticsearch $response */
        $response = $this->client->openPointInTime($params);
        $rawResult = $response->asArray();

        return $rawResult['id'];
    }

    public function close(string $pointInTimeId): self
    {
        $this->client->closePointInTime([
            'body' => [
                'id' => $pointInTimeId,
            ],
        ]);

        return $this;
    }
}

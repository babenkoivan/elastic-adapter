<?php declare(strict_types=1);

namespace OpenSearch\Adapter\Search;

use OpenSearch\Adapter\Client;

final class PointInTimeManager
{
    use Client;

    public function open(string $indexName, ?string $keepAlive = null): string
    {
        $params = ['index' => $indexName];

        if (isset($keepAlive)) {
            $params['keep_alive'] = $keepAlive;
        }

        $rawResult = $this->client->openPointInTime($params);

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

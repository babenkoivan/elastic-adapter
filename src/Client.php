<?php declare(strict_types=1);

namespace OpenSearch\Adapter;

use OpenSearch\Client as OpenSearchClient;
use OpenSearch\Laravel\Client\ClientBuilderInterface as OpenSearchClientBuilderInterface;

trait Client
{
    private OpenSearchClientBuilderInterface $clientBuilder;
    private OpenSearchClient $client;

    public function __construct(OpenSearchClientBuilderInterface $clientBuilder)
    {
        $this->clientBuilder = $clientBuilder;
        $this->client = $clientBuilder->default();
    }

    public function connection(string $name): self
    {
        $self = clone $this;
        $self->client = $self->clientBuilder->connection($name);
        return $self;
    }
}

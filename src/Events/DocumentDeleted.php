<?php

namespace ElasticAdapter\Events;

class DocumentDeleted
{
    /** @var string */
    private $indexName;

    /** @var array */
    private $documentIds;

    public function __construct(string $indexName, array $documentIds)
    {
        $this->indexName = $indexName;
        $this->documentIds = $documentIds;
    }
}

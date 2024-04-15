# Elastic Adapter

[![Latest Stable Version](https://poser.pugx.org/babenkoivan/elastic-adapter/v/stable)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![Total Downloads](https://poser.pugx.org/babenkoivan/elastic-adapter/downloads)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![License](https://poser.pugx.org/babenkoivan/elastic-adapter/license)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![Tests](https://github.com/babenkoivan/elastic-adapter/workflows/Tests/badge.svg)](https://github.com/babenkoivan/elastic-adapter/actions?query=workflow%3ATests)
[![Code style](https://github.com/babenkoivan/elastic-adapter/workflows/Code%20style/badge.svg)](https://github.com/babenkoivan/elastic-adapter/actions?query=workflow%3A%22Code+style%22)
[![Static analysis](https://github.com/babenkoivan/elastic-adapter/workflows/Static%20analysis/badge.svg)](https://github.com/babenkoivan/elastic-adapter/actions?query=workflow%3A%22Static+analysis%22)
[![Donate PayPal](https://img.shields.io/badge/donate-paypal-blue)](https://paypal.me/babenkoi)

<p align="center">
    <a href="https://ko-fi.com/ivanbabenko" target="_blank"><img src="https://ko-fi.com/img/githubbutton_sm.svg" alt="Support the project!"></a>
</p>

---

Elastic Adapter is an adapter for the official PHP Elasticsearch client. It's designed to simplify basic index and document
operations.

## Contents

* [Compatibility](#compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Index Management](#index-management)
* [Document Management](#document-management)
* [Point in Time Management](#point-in-time-management)

## Compatibility

The current version of Elastic Adapter has been tested with the following configuration:

* PHP 7.4-8.x
* Elasticsearch 8.x
* Laravel 6.x-10.x

## Installation

The library can be installed via Composer:

```bash
composer require babenkoivan/elastic-adapter
```

## Configuration

Elastic Adapter uses [babenkoivan/elastic-client](https://github.com/babenkoivan/elastic-client) as a dependency.
To change the client settings you need to publish the configuration file first:

```bash
php artisan vendor:publish --provider="Elastic\Client\ServiceProvider"
```

In the newly created `config/elastic.client.php` file you can define the default connection name and describe multiple
connections using configuration hashes. Please, refer to
the [elastic-client documentation](https://github.com/babenkoivan/elastic-client) for more details.

## Index Management

`\Elastic\Adapter\Indices\IndexManager` is used to manipulate indices.

### Create

Create an index, either with the default settings and mapping:

```php
$index = new \Elastic\Adapter\Indices\Index('my_index');

$indexManager->create($index);
```

or configured according to your needs:

```php
$mapping = (new \Elastic\Adapter\Indices\Mapping())
    ->text('title', [
        'boost' => 2,
    ])
    ->keyword('tag', [
        'null_value' => 'NULL'
    ])
    ->geoPoint('location')
    ->dynamic(true)
    ->dynamicTemplate('no_doc_values', [
        'match_mapping_type' => '*',
        'mapping' => [
            'type' => '{dynamic_type}',
            'doc_values' => false,
        ],
    ]);

$settings = (new \Elastic\Adapter\Indices\Settings())
    ->index([
        'number_of_replicas' => 2,
        'refresh_interval' => -1
    ]);

$index = new \Elastic\Adapter\Indices\Index('my_index', $mapping, $settings);

$indexManager->create($index);
```

Alternatively, you can create an index using raw input:

```php
$mapping = [
    'properties' => [
        'title' => [
            'type' => 'text'
        ]   
    ]
];

$settings = [
    'number_of_replicas' => 2
];

$indexManager->createRaw('my_index', $mapping, $settings);
```

### Drop

Delete an index:

```php
$indexManager->drop('my_index');
```

### Put Mapping

Update an index mapping using builder:

```php
$mapping = (new \Elastic\Adapter\Indices\Mapping())
    ->text('title', [
        'boost' => 2,
    ])
    ->keyword('tag', [
        'null_value' => 'NULL'
    ])
    ->geoPoint('location');

$indexManager->putMapping('my_index', $mapping);
```

or using raw input:

```php
$mapping = [
    'properties' => [
        'title' => [
            'type' => 'text'
        ]   
    ]
];

$indexManager->putMappingRaw('my_index', $mapping);
```

### Put Settings

Update an index settings using builder:

```php
$settings = (new \Elastic\Adapter\Indices\Settings())
    ->analysis([
        'analyzer' => [
            'content' => [
                'type' => 'custom',
                'tokenizer' => 'whitespace'    
            ]
        ]
    ]);

$indexManager->putSettings('my_index', $settings);
```

or using raw input:

```php
$settings = [
    'number_of_replicas' => 2
];

$indexManager->putSettingsRaw('my_index', $settings);
```

### Exists

Check if an index exists:

```php
$indexManager->exists('my_index');
```

### Open

Open an index:

```php
$indexManager->open('my_index');
```

### Close

Close an index:

```php
$indexManager->close('my_index');
```

### Put Alias

Create an alias:

```php
$alias = new \Elastic\Adapter\Indices\Alias('my_alias', true, [
    'term' => [
        'user_id' => 12,
    ],
]);

$indexManager->putAlias('my_index', $alias);
```

The same with raw input:

```php
$settings = [
    'is_write_index' => true,
    'filter' => [
        'term' => [
            'user_id' => 12,
        ],
    ],
];

$indexManager->putAliasRaw('my_index', 'my_alias', $settings);
```

### Get Aliases

Get index aliases:

```php
$indexManager->getAliases('my_index');
```

### Delete Alias

Delete an alias:

```php
$indexManager->deleteAlias('my_index', 'my_alias');
```

### Connection

Switch Elasticsearch connection:

```php
$indexManager->connection('my_connection');
```

## Document Management

`\Elastic\Adapter\Documents\DocumentManager` is used to manage and search documents. 

### Index

Add a document to the index:

```php
$documents = collect([
    new \Elastic\Adapter\Documents\Document('1', ['title' => 'foo']),
    new \Elastic\Adapter\Documents\Document('2', ['title' => 'bar']),
]);

$documentManager->index('my_index', $documents);
```

There is also an option to refresh index immediately:

```php
$documentManager->index('my_index', $documents, true);
```

Finally, you can set a custom routing:

```php
$routing = (new \Elastic\Adapter\Documents\Routing())
    ->add('1', 'value1')
    ->add('2', 'value2');

$documentManager->index('my_index', $documents, false, $routing);
```

### Delete

Remove a document from the index:

```php
$documentIds = ['1', '2'];

$documentManager->delete('my_index', $documentIds);
```

If you want the index to be refreshed immediately pass `true` as the third argument:

```php
$documentManager->delete('my_index', $documentIds, true);
```

You can also set a custom routing:

```php
$routing = (new \Elastic\Adapter\Documents\Routing())
    ->add('1', 'value1')
    ->add('2', 'value2');

$documentManager->delete('my_index', $documentIds, false, $routing);
```

Finally, you can delete documents using query:

```php
$documentManager->deleteByQuery('my_index', ['match_all' => new \stdClass()]);
```

### Search

Search documents in the index:

```php
// configure search parameters
$searchParameters = new \Elastic\Adapter\Search\SearchParameters();

// specify indices to search in
$searchParameters->indices(['my_index1', 'my_index2']);

// define the query
$searchParameters->query([
    'match' => [
        'message' => 'test'
    ]
]);

// configure highlighting
$searchParameters->highlight([
    'fields' => [
        'message' => [
            'type' => 'plain',
            'fragment_size' => 15,
            'number_of_fragments' => 3,
            'fragmenter' => 'simple'
        ]
    ]
]);

// add suggestions
$searchParameters->suggest([
    'message_suggest' => [
        'text' => 'test',
        'term' => [
            'field' => 'message'
        ]
    ]
]);

// enable source filtering
$searchParameters->source(['message', 'post_date']);

// collapse fields
$searchParameters->collapse([
    'field' => 'user'
]);

// aggregate data
$searchParameters->aggregations([
    'max_likes' => [
        'max' => [
            'field' => 'likes'
        ]
    ]
]);

// sort documents
$searchParameters->sort([
    ['post_date' => ['order' => 'asc']],
    '_score'
]);

// rescore documents
$searchParameters->rescore([
    'window_size' => 50,
    'query' => [
        'rescore_query' => [
            'match_phrase' => [
                'message' => [
                    'query' => 'the quick brown',
                    'slop' => 2,
                ],
            ],
        ],
        'query_weight' => 0.7,
        'rescore_query_weight' => 1.2,
    ]
]);

// add a post filter
$searchParameters->postFilter([
    'term' => [
        'cover' => 'hard'
    ]
]);

// track total hits
$searchParameters->trackTotalHits(true);

// track scores
$searchParameters->trackScores(true);

// script fields
$searchParameters->scriptFields([
    'my_doubled_field' => [
        'script' => [
            'lang' => 'painless',
            'source' => 'doc[params.field] * params.multiplier',
            'params' => [
                'field' => 'my_field',
                'multiplier' => 2,
            ],
        ],
    ],
]);

// boost indices
$searchParameters->indicesBoost([
    ['my-alias' => 1.4],
    ['my-index' => 1.3],
]);

// define the search type
$searchParameters->searchType('query_then_fetch');

// set the preference
$searchParameters->preference('_local');

// use pagination
$searchParameters->from(0)->size(20);

// search after
$searchParameters->pointInTime([
    'id' => '46ToAwMDaWR5BXV1',
    'keep_alive' => '1m',
]);

$searchParameters->searchAfter([
    '2021-05-20T05:30:04.832Z',
    4294967298,
]);

// use custom routing
$searchParameters->routing(['user1', 'user2']);

// enable explanation
$searchParameters->explain(true);

// set maximum number of documents to collect for each shard
$searchParameters->terminateAfter(10);

// enable caching
$searchParameters->requestCache(true);

// enable scroll search - the unit can be 'd', 'h', 'm', 's', 'ms', 'micros', 'nanos'
$searchParameters->scroll('1m');

// perform the search and get the result
$searchResult = $documentManager->search($searchParameters);

// get the total number of matching documents
$total = $searchResult->total(); 

// get the corresponding hits
$hits = $searchResult->hits();

// every hit provides access to the related index name, the score, the document, the highlight and more
// in addition, you can get a raw representation of the hit
foreach ($hits as $hit) {
    $indexName = $hit->indexName();
    $score = $hit->score();
    $document = $hit->document();
    $highlight = $hit->highlight();
    $innerHits = $hit->innerHits();
    $innerHitsTotal = $hit->innerHitsTotal();
    $raw = $hit->raw();
    
    // get an explanation 
    $explanation = $searchResult->explanation();
    
    // every explanation includes a value, a description and details
    // it is also possible to get its raw representation
    $value = $explanation->value();
    $description = $explanation->description();
    $details = $explanation->details();
    $raw = $explanation->raw();
}

// get suggestions
$suggestions = $searchResult->suggestions();

// get aggregations
$aggregations = $searchResult->aggregations();
```

### Connection

Switch Elasticsearch connection:

```php
$documentManager->connection('my_connection');
```

## Point in Time Management

`\Elastic\Adapter\Search\PointInTimeManager` is used to control points in time.

### Open

Open a point in time:

```php
$pointInTimeId = $pointInTimeManager->open('my_index', '1m');
```
### Close

Close a point in time:

```php
$pointInTimeManager->close($pointInTimeId);
```

### Connection

Switch Elasticsearch connection:

```php
$pointInTimeManager->connection('my_connection');
```

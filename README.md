# Elastic Adapter

[![Latest Stable Version](https://poser.pugx.org/babenkoivan/elastic-adapter/v/stable)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![Total Downloads](https://poser.pugx.org/babenkoivan/elastic-adapter/downloads)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![License](https://poser.pugx.org/babenkoivan/elastic-adapter/license)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![Tests](https://github.com/babenkoivan/elastic-adapter/workflows/Tests/badge.svg)](https://github.com/babenkoivan/elastic-adapter/actions?query=workflow%3ATests)
[![Code style](https://github.com/babenkoivan/elastic-adapter/workflows/Code%20style/badge.svg)](https://github.com/babenkoivan/elastic-adapter/actions?query=workflow%3A%22Code+style%22)
[![Static analysis](https://github.com/babenkoivan/elastic-adapter/workflows/Static%20analysis/badge.svg)](https://github.com/babenkoivan/elastic-adapter/actions?query=workflow%3A%22Static+analysis%22)
[![Donate PayPal](https://img.shields.io/badge/donate-paypal-blue)](https://paypal.me/babenkoi)

<p>
    <a href="https://www.buymeacoffee.com/ivanbabenko" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-green.png" alt="Buy Me A Coffee" height="50"></a>
</p>

---

Elastic Adapter is an adapter for official PHP Elasticsearch client. It's designed to simplify basic index and document 
operations.

## Contents

* [Compatibility](#compatibility)
* [Installation](#installation) 
* [Index Management](#index-management)
* [Document Management](#document-management)

## Compatibility

The current version of Elastic Adapter has been tested with the following configuration:

* PHP 7.2-7.4
* Elasticsearch 7.x

## Installation

The library can be installed via Composer:

```bash
composer require babenkoivan/elastic-adapter
```

## Index Management

`IndexManager` can be used to manipulate indices. It uses Elasticsearch client as a dependency,
therefore you need to initiate the client before you create an `IndexManager` instance:

```php
$client = \Elasticsearch\ClientBuilder::fromConfig([
  'hosts' => [
      'localhost:9200'
  ]
]);

$indexManager = new \ElasticAdapter\Indices\IndexManager($client);
``` 

The manager provides a list of useful methods, which are listed below. 

### Create

Create an index, either with the default settings and mapping:

```php
$index = new \ElasticAdapter\Indices\Index('my_index');

$indexManager->create($index);
```

or configured according to your needs:

```php
$mapping = (new \ElasticAdapter\Indices\Mapping())
    ->text('title', [
        'boost' => 2,
    ])
    ->keyword('tag', [
        'null_value' => 'NULL'
    ])
    ->geoPoint('location')
    ->dynamicTemplate('no_doc_values', [
        'match_mapping_type' => '*',
        'mapping' => [
            'type' => '{dynamic_type}',
            'doc_values' => false,
        ],
    ]);

$settings = (new \ElasticAdapter\Indices\Settings())
    ->index([
        'number_of_replicas' => 2,
        'refresh_interval' => -1
    ]);

$index = new \ElasticAdapter\Indices\Index('my_index', $mapping, $settings);

$indexManager->create($index);
```

### Drop

Delete an index:

```php
$indexManager->drop('my_index');
```

### Put Mapping

Update an index mapping:

```php
$mapping = (new \ElasticAdapter\Indices\Mapping())
    ->text('title', [
        'boost' => 2,
    ])
    ->keyword('tag', [
        'null_value' => 'NULL'
    ])
    ->geoPoint('location');

$indexManager->putMapping('my_index', $mapping);
```

### Put Settings

Update an index settings:

```php
$settings = (new \ElasticAdapter\Indices\Settings())
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

## Document Management

Similarly to `IndexManager`, the `DocumentManager` class also depends on Elasticsearch client:

```php
$client = \Elasticsearch\ClientBuilder::fromConfig([
  'hosts' => [
      'localhost:9200'
  ]
]);

$documentManager = new \ElasticAdapter\Documents\DocumentManager($client);
``` 

### Index

Add a document to the index:

```php
$documents = [
    new ElasticAdapter\Documents\Document('1', ['title' => 'foo']),
    new ElasticAdapter\Documents\Document('2', ['title' => 'bar']),
];

$documentManager->index('my_index', $documents);
```

There is also an option to refresh index immediately:

```php
$documentManager->index('my_index', $documents, true);
```

### Delete

Remove a document from the index:

```php
$documents = [
    new ElasticAdapter\Documents\Document('1', ['title' => 'foo']),
    new ElasticAdapter\Documents\Document('2', ['title' => 'bar']),
];

$documentManager->delete('my_index', $documents);
```

If you want the index to be refreshed immediately pass `true` as the third argument:

```php
$documentManager->delete('my_index', $documents, true);
```

You can also delete documents using query:

```php
$documentManager->deleteByQuery('my_index', ['match_all' => new \stdClass()]);
```

### Search

Search documents in the index:

```php
// create a search request
$request = new \ElasticAdapter\Search\SearchRequest([
    'match' => [
        'message' => 'test'
    ]
]);

// configure highlighting
$request->setHighlight([
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
$request->setSuggest([
    'message_suggest' => [
        'text' => 'test',
        'term' => [
            'field' => 'message'
        ]
    ]
]);

// enable source filtering
$request->setSource(['message', 'post_date']);

// collapse fields
$request->setCollapse([
    'field' => 'user'
]);

// aggregate data
$request->setAggregations([
    'max_likes' => [
        'max' => [
            'field' => 'likes'
        ]
    ]
]);

// sort documents
$request->setSort([
    ['post_date' => ['order' => 'asc']],
    '_score'
]);

// add a post filter
$request->setPostFilter([
    'term' => [
        'cover' => 'hard'
    ]
]);

// track total hits
$request->setTrackTotalHits(true);

// track scores
$request->setTrackScores(true);

// script fields
$request->setScriptFields([
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
$request->setIndicesBoost([
    ['my-alias' => 1.4],
    ['my-index' => 1.3],
]);

// use pagination
$request->setFrom(0)->setSize(20);

// execute the search request and get the response
$response = $documentManager->search('my_index', $request);

// get the total number of matching documents
$total = $response->getHitsTotal(); 

// get the corresponding hits
$hits = $response->getHits();

// every hit provides an access to the related index name, the score, the document and the highlight
// in addition, you can get a raw representation of the hit
foreach ($hits as $hit) {
    $indexName = $hit->getIndexName();
    $score = $hit->getScore();
    $document = $hit->getDocument();
    $highlight = $hit->getHighlight();
    $raw = $hit->getRaw();
}

// get the suggestions
$suggestions = $response->getSuggestions();

// get the aggregations
$aggregations = $response->getAggregations();
```

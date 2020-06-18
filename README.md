# Elastic Adapter

[![Latest Stable Version](https://poser.pugx.org/babenkoivan/elastic-adapter/v/stable)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![Total Downloads](https://poser.pugx.org/babenkoivan/elastic-adapter/downloads)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![License](https://poser.pugx.org/babenkoivan/elastic-adapter/license)](https://packagist.org/packages/babenkoivan/elastic-adapter)
[![Build Status](https://travis-ci.com/babenkoivan/elastic-adapter.svg?&branch=master)](https://travis-ci.com/babenkoivan/elastic-adapter)
[![Donate PayPal](https://img.shields.io/badge/donate-paypal-blue)](https://paypal.me/babenkoi)
[![Donate Amazon](https://img.shields.io/badge/donate-amazon-black)](https://www.amazon.de/Amazon-de-e-Gift-Voucher-Various-Designs/dp/B07Q1JNC7R)

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

Creates an index, either with the default settings and mapping:

```php
$index = new \ElasticAdapter\Indices\Index('my_index');

$indexManager->create($index);
```

or configured accordingly to your needs:

```php
$mapping = (new \ElasticAdapter\Indices\Mapping())
    ->text('title', [
        'boost' => 2,
    ])
    ->keyword('tag', [
        'null_value' => 'NULL'
    ])
    ->geoPoint('location');

$settings = (new \ElasticAdapter\Indices\Settings())
    ->index([
        'number_of_replicas' => 2,
        'refresh_interval' => -1
    ]);

$index = new \ElasticAdapter\Indices\Index('my_index', $mapping, $settings);

$indexManager->create($index);
```

### Drop

Deletes an index:

```php
$indexManager->drop('my_index');
```

### Put Mapping

Updates an index mapping:

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

Updates an index settings:

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

Checks if an index exists:

```php
$indexManager->exists('my_index');
```

### Open

Opens an index:

```php
$indexManager->open('my_index');
```

### Close

Closes an index:

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

Adds a document to an index:

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

Removes a document from index:

```php
$documents = [
    new ElasticAdapter\Documents\Document('1', ['title' => 'foo']),
    new ElasticAdapter\Documents\Document('2', ['title' => 'bar']),
];

$documentManager->delete('my_index', $documents);
```

If you want an index to be refreshed immediately pass `true` as the third argument:

```php
$documentManager->delete('my_index', $documents, true);
```

You can also delete documents using query:

```php
$documentManager->deleteByQuery('my_index', ['match_all' => new \stdClass()]);
```

### Search

Finds documents in an index:

```php
$request = new \ElasticAdapter\Search\SearchRequest([
    'match' => [
        'message' => 'test'
    ]
]);

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

$request->setSuggest([
    'my_suggest' => [
        'text' => 'test',
        'term' => [
            'field' => 'message'
        ]
    ]
]);

$request->setSource(['message', 'post_date']);

$request->setCollapse([
    'field' => 'user'
]);

$request->setSort([
    ['post_date' => ['order' => 'asc']],
    '_score'
]);

$request->setFrom(0)->setSize(20);

$response = $documentManager->search('my_index', $request);

// total number of matched documents
$total = $response->getHitsTotal(); 

// corresponding hits
$hits = $response->getHits();

// document, highlight or raw representation of the hit
foreach ($hits as $hit) {
    $document = $hit->getDocument();
    $highlight = $hit->getHighlight();
    $raw = $hit->getRaw();
}

// suggestions
$suggestions = $response->getSuggestions();
```

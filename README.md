# Elastic Adapter

[![Build Status](https://travis-ci.com/babenkoivan/elastic-adapter.svg?token=tL2AyZUSS9biRsKPg7fp&branch=master)](https://travis-ci.com/babenkoivan/elastic-adapter)

---

An adapter for official PHP Elasticsearch client. It's designed to simplify basic index and document 
operations.

## Contents

* [Installation](#installation) 
* [Index management](#index-management)
* [Document management](#document-management)

## Installation

The library can be installed via Composer:

```bash
composer require babenkoivan/elastic-adapter
```

## Index management

The `IndexManager` class can be used for indices manipulation. It uses Elasticsearch client as a dependency, so make sure it's properly configured and passed in the constructor:

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

You can create an index with the default settings and mapping:

```php
$index = new \ElasticAdapter\Indices\Index('my_index');

$indexManager->create($index);
```

or configure the index to fulfill your needs:

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
    ->numberOfReplicas(2)
    ->refreshInterval(-1);

$index = new \ElasticAdapter\Indices\Index('my_index', $mapping, $settings);

$indexManager->create($index);
```

### Drop

You can drop an index by its name:

```php
$indexManager->drop('my_index');
```

### Put Mapping

You can update an index mapping:

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

You can update an index settings:

```php
$settings = (new \ElasticAdapter\Indices\Settings())
    ->numberOfReplicas(2)
    ->refreshInterval(-1);

$indexManager->putSettings('my_index', $settings);
```

### Exists

You can check an index existence:

```php
$indexManager->exists('my_index');
```

### Open

You can open an index:

```php
$indexManager->open('my_index');
```

### Close

You can close an index:

```php
$indexManager->close('my_index');
```

## Document management

Similarly to `IndexManager`, the `DocumentManager` class also depends on Elasticsearch client:

```php
$client = \Elasticsearch\ClientBuilder::fromConfig([
  'hosts' => [
      'localhost:9200'
  ]
]);

$documentManager = new \ElasticAdapter\Documents\DocumentManager($client);
``` 

The manager makes typical operations over documents easier.

### Index

You can index documents:

```php
$documents = [
    new ElasticAdapter\Documents\Document('1', ['title' => 'foo']),
    new ElasticAdapter\Documents\Document('2', ['title' => 'bar']),
];

$documentManager->index('my_index', $documents);
```

You can also force Elasticsearch to refresh index immediately:

```php
$documentManager->index('my_index', $documents, true);
```

### Delete

You can delete documents from index:

```php
$documentIds = ['1', '2'];

$documentManager->delete('my_index', $documentIds);
```

You can refresh index immediately if needed:

```php
$documentManager->delete('my_index', $documentIds, true);
```

### Search

You can search documents:

```php
$request = new \ElasticAdapter\Search\SearchRequest([
    'match_phrase' => ['message' => 'number 1']
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

$request->setSort([
    ['post_date' => ['order' => 'asc']],
    '_score'
]);

$request->setFrom(0)->setSize(20);

$response = $documentManager->search('my_index', $request);

// you can retrieve total number of results
$total = $response->getHitsTotal();

// you can retrieve highlight and document for every hit
$hits = $response->getHits();

foreach ($hits as $hit) {
    $document = $hit->getDocument();
    $highlight = $hit->getHighlight();
}
```

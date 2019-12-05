# Elastic Adapter

[![Build Status](https://travis-ci.com/babenkoivan/elastic-adapter.svg?token=tL2AyZUSS9biRsKPg7fp&branch=master)](https://travis-ci.com/babenkoivan/elastic-adapter)

---

This is an adapter for official PHP client for Elasticsearch. It designed to simplify basic index and document 
operations.

## Contents

* [Installation](#installation) 
* [Index management](#index-management)

## Installation

You can install the library using composer:

```bash
composer require babenkoivan/elastic-adapter
```

## Index management

You can use the `IndexManager` class to manipulate indices. Before you start though, make sure you have 
configured Elasticsearch client:

```php
$client = \Elasticsearch\ClientBuilder::fromConfig([
  'hosts' => [
      'localhost:9200'
  ]
]);

$indexManager = new \ElasticAdapter\Indices\IndexManager($client);
``` 

### Create

You can created an index with default settings and mapping:

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

You can delete an index by its name:

```php
$indexManager->drop('my_index');
```

### Put Mapping

You can update index mapping:

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

You can update index settings:

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

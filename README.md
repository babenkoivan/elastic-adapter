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

Elastic Adapter is an adapter for official PHP Elasticsearch client. It's designed to simplify basic index and document 
operations.

## Contents

* [Compatibility](#compatibility)
* [Installation](#installation) 
* [Index Management](#index-management)
* [Document Management](#document-management)

## Compatibility

The current version of Elastic Adapter has been tested with the following configuration:

* PHP 7.3-8.0
* Elasticsearch 7.x
* Laravel 6.x-8.x

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

The manager provides a set of useful methods, which are listed below. 

### Create

Create an index, either with the default settings and mapping:

```php
$index = new \ElasticAdapter\Indices\IndexBlueprint('my_index');

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

$index = new \ElasticAdapter\Indices\IndexBlueprint('my_index', $mapping, $settings);

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
$alias = new \ElasticAdapter\Indices\Alias('my_alias', [
    'term' => [
        'user_id' => 12,
    ],
]);

$indexManager->putAlias('my_index', $alias);
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
$documents = collect([
    new ElasticAdapter\Documents\Document('1', ['title' => 'foo']),
    new ElasticAdapter\Documents\Document('2', ['title' => 'bar']),
]);

$documentManager->index('my_index', $documents);
```

There is also an option to refresh index immediately:

```php
$documentManager->index('my_index', $documents, true);
```

Finally, you can set a custom routing:

```php
$routing = (new ElasticAdapter\Documents\Routing())
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
$routing = (new ElasticAdapter\Documents\Routing())
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
// create a search request
$request = new \ElasticAdapter\Search\SearchRequest([
    'match' => [
        'message' => 'test'
    ]
]);

// configure highlighting
$request->highlight([
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
$request->suggest([
    'message_suggest' => [
        'text' => 'test',
        'term' => [
            'field' => 'message'
        ]
    ]
]);

// enable source filtering
$request->source(['message', 'post_date']);

// collapse fields
$request->collapse([
    'field' => 'user'
]);

// aggregate data
$request->aggregations([
    'max_likes' => [
        'max' => [
            'field' => 'likes'
        ]
    ]
]);

// sort documents
$request->sort([
    ['post_date' => ['order' => 'asc']],
    '_score'
]);

// rescore documents
$request->rescore([
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
$request->postFilter([
    'term' => [
        'cover' => 'hard'
    ]
]);

// track total hits
$request->trackTotalHits(true);

// track scores
$request->trackScores(true);

// script fields
$request->scriptFields([
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
$request->indicesBoost([
    ['my-alias' => 1.4],
    ['my-index' => 1.3],
]);

// define the search type
$request->searchType('query_then_fetch');

// set the preference
$request->preference('_local');

// use pagination
$request->from(0)->size(20);

// execute the search request and get the response
$response = $documentManager->search('my_index', $request);

// get the total number of matching documents
$total = $response->total(); 

// get the corresponding hits
$hits = $response->hits();

// every hit provides access to the related index name, the score, the document, the highlight and the inner hits
// in addition, you can get a raw representation of the hit
foreach ($hits as $hit) {
    $indexName = $hit->indexName();
    $score = $hit->score();
    $document = $hit->document();
    $highlight = $hit->highlight();
    $innerHits = $hit->innerHits();
    $raw = $hit->raw();
}

// get the suggestions
$suggestions = $response->suggestions();

// get the aggregations
$aggregations = $response->aggregations();
```

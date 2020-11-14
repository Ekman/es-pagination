# Elasticsearch pagination

[![Build Status](https://travis-ci.org/Ekman/es-pagination.svg?branch=master)](https://travis-ci.org/Ekman/es-pagination)
[![Coverage Status](https://coveralls.io/repos/github/Ekman/es-pagination/badge.svg?branch=master)](https://coveralls.io/github/Ekman/es-pagination?branch=master)

A library to deep paginate an Elasticsearch search operation. There are [three ways to paginate](https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html):

1. [Scroll](#scroll)
2. [From](#from)
3. [Search after](#search-after)

Which one to use depends on the context, [read more in the Elasticsearch documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html).

The library will get `pageSize` amount of hits in memory at the same time, which means a lower amount will result in less memory used but more requests to Elasticsearch (and the opposite). Never will it fully exhaust
an index before returning the results. 

## Usage

The first step is to construct an `$elasticsearchClient` (instance of `Elasticsearch\Client`) which you can read more about in the [Elasticsearch official PHP driver](https://github.com/elastic/elasticsearch-php).

### Scroll

```php
use Nekman\EsPagination\CursorFactories\EsScrollCursorFactory;

$cursorFactory = new EsScrollCursorFactory(
    $elasticsearchClient,
    $pageSize = 1000,
    $scrollDuration = "1m"
);

$params = [
    /*
     * Same params as a normal Elasticsearch search operation.
     * See Elasticsearch documentation for more information.
     */
];

$cursor = $cursorFactory->hits($params);

foreach ($cursor as $hit) {
    echo "Hit {$hit['_id']}";
}
```

### From

```php
use Nekman\EsPagination\CursorFactories\EsFromCursorFactory;

$cursorFactory = new EsFromCursorFactory(
    $elasticsearchClient,
    $pageSize = 1000
);

$params = [
    /*
     * Same params as a normal Elasticsearch search operation.
     * See Elasticsearch documentation for more information.
     */
];

$cursor = $cursorFactory->hits($params);

foreach ($cursor as $hit) {
    echo "Hit {$hit['_id']}";
}
```

### Search after

```php
use Nekman\EsPagination\CursorFactories\EsSearchAfterCursorFactory;

$cursorFactory = new EsSearchAfterCursorFactory(
    $elasticsearchClient,
    $pageSize = 1000
);

$params = [
    /*
     * Same params as a normal Elasticsearch search operation.
     * See Elasticsearch documentation for more information.
     */
];

$cursor = $cursorFactory->hits($params);

foreach ($cursor as $hit) {
    echo "Hit {$hit['_id']}";
}
```

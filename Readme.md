# Elasticsearch pagination

A library to deep paginate an Elasticsearch search operation. There are [three ways to paginate](https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html),
[scroll](#scroll), [from](#from) and [search after](#search-after). Which one to use depends on the context.

The library will get `pageSize` amount of hits in memory at the same time, which means a lower amount will result in less memory being used but more requests to Elasticsearch (and the opposite). Never will it fully exhaust
an index before returning the results. 

## Usage

The first step is to construct an `$elasticsearchClient` (instance of `Elasticsearch\Client`) which you can read more about in the [Elasticsearch official PHP driver](https://github.com/elastic/elasticsearch-php). 

### Scroll

```php
use Nekman\EsPagination\EsScrollCursorFactory;

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
use Nekman\EsPagination\EsFromCursorFactory;

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

To be written.

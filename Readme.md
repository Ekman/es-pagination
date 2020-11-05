# Elasticsearch pagination

A library to deep paginate an Elasticsearch search operation. There are [two ways to deep paginate](https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html),
scroll and search after. Which one to use depends on the context.

The library will get `pageSize` amount of hits in memory at the same time, which means a lower amount will result in less memory being used but more requests to Elasticsearch (and the opposite). Never will it fully exhaust
an index before returning the results. 

## Usage

Construct an instance of a `Elasticsearch\Client` which you can read more about on [Elasticsearch official PHP driver](https://github.com/elastic/elasticsearch-php). 

### Scroll

```php
use Stockfiller\EsPagination\EsScrollCursorFactory;

$cursorFactory = new EsScrollCursorFactory(
    $elasticsearchClient,
    $size = 1000,
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

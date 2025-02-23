# Sambo-Search PHP Client

![Unit Tests](https://github.com/sambo-search/sambo-search-client/actions/workflows/ci.yml/badge.svg)

## Synopsis

This is a simple PHP client for the Sambo-Search API. It simplifies the usage of the API by providing many
helper functions and easy access to the API suite.

## Prerequisites

* PHP >= 8.1
* Composer

## Install

Install via Composer:

```bash
composer require sambo-search/sambo-search-client
```

## Usage

*If you are not sure where to find your shops' public key,
open the Sambo-Search Dashboard and navigate to [Account > Shops](https://dashboard.sambo-search.com/account/shops)*.

### Sending a simple search request

1. Create a new `Client` instance:
    ```php
    $client = \SamboSearch\Client\Client::getInstance([
        'public_key' => '<your-public-key-here>'
    ]);
    ```
2. Build your search request:
    ```php
    $request = $client->buildSearchRequest();
    $request->setQuery('backpack') // Search query
        ->setFilter('color', 'Black'); // Filters
    ```
3. Send request and parse response:
    ```php
    /** @var \SamboSearch\Client\Response\SearchResponse $response */
    $response = $client->send($request);

    $products = $response->getProducts();
    foreach ($products as $product) {
        echo 'ID: ' . $product->getId() . "\n<br>";
        echo 'Name: ' . $product->getName() . "\n<br>";
    }
    ```

Full example (same as from above, but with imports):

```php
<?php

use SamboSearch\Client\Client;
use SamboSearch\Client\Response\SearchResponse;

$client = Client::getInstance(['public_key' => '<your-public-key-here>']);

// Apply any filters or sorting
$request = $client->buildSearchRequest();
$request->setQuery('backpack') // Search query
    ->setFilter('color', 'Black'); // Filters

/** @var SearchResponse $response */
$response = $client->send($request);

// Access product details
$products = $response->getProducts();
foreach ($products as $product) {
    echo 'ID: ' . $product->getId() . "\n<br>";
    echo 'Name: ' . $product->getName() . "\n<br>";
}
```

## Documentation

Most methods include a PHPDoc block that simply explains their usage. Here is an example from the SearchRequest class:

```php
/**
 * Selects a filter with the given identifier and value. Examples:
 *
 * $searchRequest->addFilter(['manufacturer', 'Sambo-Search']); // /api/v1/search?manufacturer=Sambo-Search
 * $searchRequest->addFilter(['color', 'Green|Yellow']); // /api/v1/search?color=Green%7CYellow
 * $searchRequest->addFilter(['color', ['Green', 'Yellow']]); // same /api/v1/search?color=Green%7CYellow
 * $searchRequest->addFilter([
 *     'categories',
 *     'Dogs|Food|Wet Food'
 * ]); // /api/v1/search?categories=Dogs%7CFood%7CWet%20Food
 * $searchRequest->addFilter(['price-', '10.99']); // min price /api/v1/search?price-=10.99
 * $searchRequest->addFilter(['price', '35.00']); // max price /api/v1/search?price=35.00
 *
*/
public function addFilter(string $identifier, string|array $values): static
```

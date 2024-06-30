# Sambo-Search PHP Client

## Synopsis

This is a simple PHP client for the Sambo-Search API. It simplifies the usage of the API by providing many
helper functions and easy access to the API suite.

## Prerequisites

* PHP >= 8.0
* Composer

## Install

Install via Composer:

```bash
composer require sambo-search/sambo-search-client
```

## Usage

Create a client with your `public_key` and get products:

```php
<?php

use SamboSearch\Client\Client;

$client = Client::getInstance(['public_key' => '<your-public-key-here>']);

$response = $client->search('backpack');

// Access product details
$products = $response->getProducts();
foreach ($products as $product) {
    echo 'ID: ' . $product->id . "\n<br>";
    echo 'Name: ' . $product->name . "\n<br>";
}
```

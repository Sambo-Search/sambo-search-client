<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response;

use SamboSearch\Client\DTO\ProductDTO;
use SamboSearch\Client\DTO\ProductsDTO;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;

class SearchResponse extends AbstractResponse
{
    private ProductsDTO $products;

    public function __construct(array $data)
    {
        parent::__construct($data);

        if (!isset($data['results'])) {
            throw new UnparsableResponseException('No results found');
        }

        $this->products = ProductsDTO::build($data['results']);
    }

    public static function build(array $data): static
    {
        return new self($data);
    }

    public function getProducts(): ProductsDTO
    {
        return $this->products;
    }
}

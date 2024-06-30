<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response;

use SamboSearch\Client\DTO\ProductDTO;

class SearchResponse extends AbstractResponse
{
    /**
     * @var ProductDTO[]
     */
    private array $products = [];

    public function __construct(mixed $parsedResponse)
    {
        parent::__construct($parsedResponse);

        foreach ($parsedResponse['results'] as $product) {
            $this->products[] = ProductDTO::build($product);
        }
    }

    public static function build(mixed $parsedResponse): static
    {
        return new self($parsedResponse);
    }

    /**
     * @return ProductDTO[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}

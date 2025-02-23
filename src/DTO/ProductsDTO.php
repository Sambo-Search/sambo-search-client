<?php

declare(strict_types=1);

namespace SamboSearch\Client\DTO;

use Iterator;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;

class ProductsDTO extends DTO implements Iterator
{
    /**
     * @var ProductDTO[]
     */
    private array $products;

    private int $position = 0;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->addProducts($data);
    }

    private function addProducts(array $data): void
    {
        foreach ($data as $productData) {
            $this->addProduct($productData);
        }
    }

    private function addProduct(array $data): void
    {
        try {
            $this->products[] = ProductDTO::build($data);
        } catch (UnparsableResponseException) {
        }
    }

    public static function build(array $data): static
    {
        return new static($data);
    }

    public function getById(string $id): ?ProductDTO
    {
        return $this->find('id', $id);
    }

    public function getByName(string $name): ?ProductDTO
    {
        return $this->find('name', $name);
    }

    public function find(string $key, $value): ?ProductDTO
    {
        return array_find($this->products, function (ProductDTO $product) use ($key, $value) {
            return $product->getData()[$key] === $value;
        });
    }

    public function current(): ProductDTO
    {
        return $this->products[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->products[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}

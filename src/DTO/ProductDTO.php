<?php

declare(strict_types=1);

namespace SamboSearch\Client\DTO;

use SamboSearch\Client\Exception\Server\UnparsableResponseException;

class ProductDTO extends DTO
{
    public string $id;
    public string $name;

    public function __construct(array $data)
    {
        parent::__construct($data);

        if ($data['type'] !== 'product') {
            throw new UnparsableResponseException('Can not parse product data');
        }

        $productData = $data['data'];

        $this->id = $productData['id'];
        $this->name = $productData['name'];
    }

    public static function build(array $data): static
    {
        return new self($data);
    }
}

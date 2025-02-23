<?php

declare(strict_types=1);

namespace SamboSearch\Client\DTO;

use SamboSearch\Client\Exception\Server\UnparsableResponseException;

class ProductDTO extends DTO
{
    public string $id;
    public string $name;

    /**
     * @throws UnparsableResponseException
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        if ($data['type'] !== 'product') {
            throw new UnparsableResponseException('Can not parse product data');
        }

        $this->data = $data['data'];

        $this->id = $this->data['id'];
        $this->name = $this->data['name'];
    }

    /**
     * @throws UnparsableResponseException
     */
    public static function build(array $data): static
    {
        return new self($data);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

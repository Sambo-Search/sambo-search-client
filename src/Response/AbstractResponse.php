<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response;

use SamboSearch\Client\DTO\MetaDTO;

abstract class AbstractResponse implements ResponseInterface
{
    private array $data;

    private ?MetaDTO $meta = null;

    abstract public static function build(array $data): static;

    public function __construct(array $data)
    {
        $this->data = $data;

        // Meta is optional
        if (!isset($data['meta'])) {
            return;
        }

        $this->meta = MetaDTO::build($data['meta']);
    }

    public function getMeta(): ?MetaDTO
    {
        return $this->meta;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->getData();
    }
}

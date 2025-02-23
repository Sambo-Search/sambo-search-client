<?php

declare(strict_types=1);

namespace SamboSearch\Client\DTO;

abstract class DTO
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    abstract public static function build(array $data): static;

    public function getData(): array
    {
        return $this->data;
    }
}

<?php

declare(strict_types=1);

namespace SamboSearch\Client\DTO;

abstract class DTO
{
    protected array $rawData;

    public function __construct(array $data)
    {
        $this->rawData = $data;
    }

    abstract public static function build(array $data): static;

    public function getRawData(): array
    {
        return $this->rawData;
    }
}

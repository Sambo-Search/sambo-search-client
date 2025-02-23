<?php

declare(strict_types=1);

namespace SamboSearch\Client\DTO;

class MetaDTO extends DTO
{
    public static function build(array $data): static
    {
        return new static($data);
    }
}

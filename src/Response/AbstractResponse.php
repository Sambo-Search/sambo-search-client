<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response;

use SamboSearch\Client\DTO\MetaDTO;

abstract class AbstractResponse implements ResponseInterface
{
    private ?MetaDTO $meta = null;

    abstract public static function build(mixed $parsedResponse): static;

    public function __construct(array $parsedResponse)
    {
        if (!isset($parsedResponse['meta'])) {
            return;
        }

        $this->meta = new MetaDTO($parsedResponse['meta']);
    }

    public function getMeta(): MetaDTO
    {
        return $this->meta;
    }
}

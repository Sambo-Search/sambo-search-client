<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response;

interface ResponseInterface
{
    public static function build(mixed $parsedResponse): static;
}

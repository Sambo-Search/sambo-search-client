<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response;

use JsonSerializable;

interface ResponseInterface extends JsonSerializable
{
    public static function build(array $data): static;
}

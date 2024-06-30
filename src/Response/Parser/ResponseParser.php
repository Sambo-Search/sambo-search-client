<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response\Parser;

use Psr\Http\Message\ResponseInterface;

interface ResponseParser
{
    public static function supports(ResponseInterface $response): bool;
    public function parse(ResponseInterface $rawResponse): mixed;
}

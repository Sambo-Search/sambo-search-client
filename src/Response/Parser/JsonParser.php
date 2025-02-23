<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response\Parser;

use Psr\Http\Message\ResponseInterface;

class JsonParser implements ParserInterface
{
    public static function supports(ResponseInterface $response): bool
    {
        return $response->getHeader('Content-Type')[0] === 'application/json';
    }

    public function parse(ResponseInterface $response): array
    {
        $content = json_decode($response->getBody()->getContents(), true);

        // TODO: Validation

        return $content;
    }
}

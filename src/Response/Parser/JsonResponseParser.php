<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response\Parser;

use Psr\Http\Message\ResponseInterface;

class JsonResponseParser implements ResponseParser
{
    public static function supports(ResponseInterface $response): bool
    {
        return $response->getHeader('Content-Type')[0] === 'application/json';
    }

    public function parse(ResponseInterface $rawResponse): array
    {
        $content = json_decode($rawResponse->getBody()->getContents(), true);

        // TODO: Validation

        return $content;
    }
}

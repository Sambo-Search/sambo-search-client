<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response\Parser;

use Psr\Http\Message\ResponseInterface;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;

class ParserFactory
{
    public static function getInstance(ResponseInterface $response): ResponseParser
    {
        if (!$response->hasHeader('Content-Type')) {
            throw new UnparsableResponseException('Header: Content-Type not set');
        }

        if (JsonResponseParser::supports($response)) {
            return new JsonResponseParser();
        }

        throw new UnparsableResponseException('Can not parse response');
    }
}

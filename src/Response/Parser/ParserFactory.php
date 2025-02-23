<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response\Parser;

use Psr\Http\Message\ResponseInterface;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;

class ParserFactory
{
    public static function getInstance(ResponseInterface $response): ParserInterface
    {
        if (!$response->hasHeader('Content-Type')) {
            throw new UnparsableResponseException('Header: Content-Type not set');
        }

        if (JsonParser::supports($response)) {
            return new JsonParser();
        }

        throw new UnparsableResponseException('Can not parse response');
    }
}

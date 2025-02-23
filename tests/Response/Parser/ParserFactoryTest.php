<?php

declare(strict_types=1);

namespace SamboSearchTest\Client\Response\Parser;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;
use SamboSearch\Client\Response\Parser\ParserFactory;

class ParserFactoryTest extends TestCase
{
    public function testParserFactoryFailsIfContentTypeIsNotSet(): void
    {
        $this->expectException(UnparsableResponseException::class);
        $this->expectExceptionMessage('Header: Content-Type not set');

        $response = new Response();

        ParserFactory::getInstance($response);
    }

    public function testParserFactoryFailsForUnknownContentType(): void
    {
        $this->expectException(UnparsableResponseException::class);
        $this->expectExceptionMessage('Can not parse response');

        $response = new Response(headers: ['Content-Type' => 'text/plain']);

        ParserFactory::getInstance($response);
    }
}

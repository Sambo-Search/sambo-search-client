<?php

declare(strict_types=1);

namespace SamboSearchTest\Client\Response\Mapper;

use PHPUnit\Framework\TestCase;
use SamboSearch\Client\Request\RequestInterface;
use SamboSearch\Client\Response\Mapper\ResponseMapper;

class ResponseMapperTest extends TestCase
{
    public function testUnknownRequestThrowsException(): void
    {
        $pattern = '/^Can\snot\smap\sunknown\srequest/';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches($pattern);

        $request = new class implements RequestInterface {
            public function getRoute(): string
            {
                return 'test';
            }

            public function getMethod(): string
            {
                return 'test';
            }

            public function getParams(): array
            {
                return [];
            }
        };

        $mapper = new ResponseMapper();
        $mapper->map($request, []);
    }
}

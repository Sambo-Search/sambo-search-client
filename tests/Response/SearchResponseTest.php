<?php

declare(strict_types=1);

namespace SamboSearchTest\Client\Response;

use PHPUnit\Framework\TestCase;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;
use SamboSearch\Client\Response\SearchResponse;

class SearchResponseTest extends TestCase
{
    public function testNoMeta(): void
    {
        $searchResponse = new SearchResponse(['results' => []]);

        $this->assertNull($searchResponse->getMeta());
    }

    public function testCanGetMetaData(): void
    {
        $searchResponse = new SearchResponse([
            'meta' => [
                'foo' => 'bar',
            ],
            'results' => [],
        ]);

        $this->assertSame('bar', $searchResponse->getMeta()->getData()['foo']);
    }

    public function testFailsIfNoResultsAreSetAtAll(): void
    {
        $this->expectException(UnparsableResponseException::class);
        $this->expectExceptionMessage('No results found');

        new SearchResponse([]);
    }
}

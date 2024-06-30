<?php

declare(strict_types=1);

namespace SamboSearchTest\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SamboSearch\Client\Client;

class ClientTest extends TestCase
{
    public function testSearchRequestIsSentAndParsed(): void
    {
        $mockResponseContent = json_encode([
            'meta' => [
                'query' => [
                    'query' => 'someQuery',
                ],
            ],
            'results' => [
                [
                    'type' => 'product',
                    'data' => [
                        'id' => '123',
                        'name' => 'Some Product',
                    ],
                ],
                [
                    'type' => 'product',
                    'data' => [
                        'id' => '456',
                        'name' => 'Some Product2',
                    ],
                ],
            ],
        ]);
        $mockClient = $this->getMockClient([
            new Response(200, ['Content-Type' => 'application/json'], $mockResponseContent),
        ]);

        $client = Client::getInstance([
            'public_key' => '1234',
            'api_client' => $mockClient,
        ]);

        $response = $client->search('someQuery');
        $products = $response->getProducts();

        $this->assertCount(2, $products);

        $expectedFirstProductId = '123';
        $expectedSecondProductId = '456';

        $this->assertSame($expectedFirstProductId, $products[0]->id);
        $this->assertSame($expectedSecondProductId, $products[1]->id);
    }

    private function getMockClient(array $responses = []): GuzzleClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        return new GuzzleClient(['handler' => $handlerStack]);
    }
}

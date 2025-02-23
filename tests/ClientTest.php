<?php

declare(strict_types=1);

namespace SamboSearchTest\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use SamboSearch\Client\Client;
use SamboSearch\Client\Exception\ConfigKeyException;
use SamboSearch\Client\Response\Parser\JsonParser;
use SamboSearch\Client\Response\Parser\ParserInterface;
use SamboSearch\Client\Response\SearchResponse;

class ClientTest extends TestCase
{
    public function testClientCreationFailsIfNoPublicKeyIsGiven(): void
    {
        $this->expectException(ConfigKeyException::class);
        $this->expectExceptionMessage('Required config key "public_key" is missing');

        Client::getInstance([]);
    }

    public function testClientCreationSuccessfulIfPublicKeyIsAvailable(): void
    {
        $expectedPublicKey = 'test1234';

        $client = Client::getInstance(['public_key' => $expectedPublicKey]);

        $this->assertSame($expectedPublicKey, $client->getPublicKey());
    }

    public function testSendSearchRequest(): void
    {
        $expectedProducts = [
            [
                'id' => '1234',
                'name' => 'Some Product',
            ],
            [
                'id' => '456',
                'name' => 'Some Product2',
            ],
        ];

        $products = [];
        foreach ($expectedProducts as $expectedProduct) {
            $products[] = $this->getMockedProduct($expectedProduct);
        }

        $expectedResponse = $this->productDataToSearchResponseData($products);

        $client = $this->getClientWithMockResponses([$this->getMockResponse($expectedResponse)]);
        $request = $client->buildSearchRequest();

        /** @var SearchResponse $response */
        $response = $client->send($request);

        $this->assertSame($expectedResponse, $response->jsonSerialize());

        $products = $response->getProducts();
        foreach ($expectedProducts as $expectedProduct) {
            $product = $products->getById($expectedProduct['id']);
            $productByName = $products->getByName($expectedProduct['name']);

            $this->assertSame($expectedProduct, $product->getData());
            $this->assertSame($product, $productByName, 'Find by name and id must yield same result');
        }
    }

    public function testSupportsCustomParser(): void
    {
        $expectedExceptionMessage = 'Custom parser is used :)';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $customParser = new class implements ParserInterface {
            public static function supports(ResponseInterface $response): bool
            {
                return true;
            }

            public function parse(ResponseInterface $response): array
            {
                throw new InvalidArgumentException('Custom parser is used :)');
            }
        };

        $data = $this->productDataToSearchResponseData([]);

        $client = $this->getClientWithMockResponses([
            $this->getMockResponse($data),
        ], ['parser' => $customParser]);

        $request = $client->buildSearchRequest();
        $client->send($request);
    }

    /**
     * @param Response[] $mockResponses
     */
    private function getClientWithMockResponses(array $mockResponses, array $clientOverrides = []): Client
    {
        $mockClient = $this->getMockClient($mockResponses);

        try {
            return Client::getInstance(array_merge([
                'public_key' => 'test1234',
                'client' => $mockClient,
            ], $clientOverrides));
        } catch (ConfigKeyException $e) {
            $this->fail($e->getMessage());
        }
    }

    private function productDataToSearchResponseData(array $productData, array $metaOverrides = []): array
    {
        $meta = array_merge([
            'query' => [
                'query' => 'test',
            ],
        ], $metaOverrides);

        return [
            'meta' => $meta,
            'results' => $productData,
        ];
    }

    private function getMockResponse(array $data, int $statusCode = 200): Response
    {
        return new Response($statusCode, ['Content-Type' => 'application/json'], json_encode($data));
    }

    private function getMockedProduct(array $overrides = []): array
    {
        $product = ['type' => 'product'];
        $product['data'] = array_merge([
            'id' => '123',
            'name' => 'Some Product',
        ], $overrides);

        return $product;
    }

    private function getMockClient(array $responses = []): GuzzleClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        return new GuzzleClient(['handler' => $handlerStack]);
    }
}

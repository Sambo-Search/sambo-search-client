<?php

declare(strict_types=1);

namespace SamboSearch\Client;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface as GuzzleResponseInterface;
use SamboSearch\Client\Exception\ConfigKeyException;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;
use SamboSearch\Client\Request\RequestInterface;
use SamboSearch\Client\Request\SearchRequest;
use SamboSearch\Client\Response\Mapper\ResponseMapper;
use SamboSearch\Client\Response\Mapper\ResponseMapperInterface;
use SamboSearch\Client\Response\Parser\ParserFactory;
use SamboSearch\Client\Response\Parser\ParserInterface;
use SamboSearch\Client\Response\ResponseInterface;
use SamboSearch\Client\Response\SearchResponse;

class Client
{
    private const BASE_URL = 'https://api.sambo-search.com/api/v1';

    /**
     * @see Client::getInstance
     */
    public function __construct(
        private string $publicKey,
        private GuzzleClient $client,
        private ResponseMapperInterface $responseMapper,
        private ?ParserInterface $parser = null
    ) {}

    /**
     * @param $config array{
     *     public_key: string,
     *     client?: GuzzleClient,
     *     mapper?: ResponseMapperInterface,
     *     parser?: ParserInterface
     * }
     *
     * @throws ConfigKeyException
     */
    public static function getInstance(array $config): static
    {
        if (!isset($config['public_key'])) {
            throw new ConfigKeyException('Required config key "public_key" is missing');
        }

        if (!isset($config['client'])) {
            $config['client'] = new GuzzleClient([
                'base_uri' => self::BASE_URL,
            ]);
        }

        if (!isset($config['mapper'])) {
            $config['mapper'] = new ResponseMapper();
        }

        return new self($config['public_key'], $config['client'], $config['mapper'], $config['parser'] ?? null);
    }

    public function buildSearchRequest(): SearchRequest
    {
        return SearchRequest::getInstance();
    }

    /**
     * Sends the given request to the Sambo-Search API. Response is mapped to an
     * equivalent PHP class with getters.
     *
     * @see SearchResponse
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $data = $this->sendAndParse($request);

        return $this->responseMapper->map($request, $data);
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    private function sendAndParse(RequestInterface $request): array
    {
        $method = $request->getMethod();
        $route = $request->getRoute();
        $params = $request->getParams();

        $response = $this->client->request($method, $route, ['query' => $params]);

        return $this->parseResponse($response);
    }

    /**
     * @throws UnparsableResponseException
     */
    private function parseResponse(GuzzleResponseInterface $response): array
    {
        if ($this->parser?->supports($response)) {
            return $this->parser->parse($response);
        }

        $parser = ParserFactory::getInstance($response);

        return $parser->parse($response);
    }
}

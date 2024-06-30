<?php

declare(strict_types=1);

namespace SamboSearch\Client;

use GuzzleHttp\Client as ApiClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;
use SamboSearch\Client\Response\Parser\ParserFactory;
use SamboSearch\Client\Response\Parser\ResponseParser;
use SamboSearch\Client\Response\SearchResponse;

class Client
{
    private const BASE_URL = 'https://sambo-search.com/api/v1';

    private string $publicKey;
    private ApiClient $client;
    private ?ResponseParser $parser;

    /**
     * @param $config array{
     *     public_key: string,
     *     api_client: ApiClient | null,
     *     parser: ResponseParser | null
     * }
     */
    public function __construct(array $config)
    {
        $this->publicKey = $config['public_key'];
        $this->client = $config['api_client'] ?? new ApiClient([
            'base_uri' => self::BASE_URL,
        ]);
        $this->parser = $config['parser'] ?? null;
    }

    public static function getInstance(array $config): static
    {
        return new self($config);
    }

    /**
     * @throws UnparsableResponseException
     * @throws GuzzleException
     */
    public function search(string $query, int $limit = 20): SearchResponse
    {
        $queryParams = [
            'query' => $query,
            'limit' => $limit,
        ];

        $response = $this->client->get('/search', ['query' => $queryParams]);
        $parsedContent = $this->doParse($response);

        return new SearchResponse($parsedContent);
    }

    /**
     * @throws UnparsableResponseException
     */
    private function doParse(ResponseInterface $response): mixed
    {
        if (!$this->parser || !$this->parser::supports($response)) {
            $this->parser = ParserFactory::getInstance($response);
        }

        return $this->parser->parse($response);
    }
}

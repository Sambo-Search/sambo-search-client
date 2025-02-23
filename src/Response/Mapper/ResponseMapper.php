<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response\Mapper;

use SamboSearch\Client\Request\RequestInterface;
use SamboSearch\Client\Request\SearchRequest;
use SamboSearch\Client\Response\ResponseInterface;
use SamboSearch\Client\Response\SearchResponse;

class ResponseMapper implements ResponseMapperInterface
{
    public function map(RequestInterface $request, array $data): ResponseInterface
    {
        switch (true) {
            case $request instanceof SearchRequest:
                return SearchResponse::build($data);
            default:
                throw new \InvalidArgumentException('Can not map unknown request ' . get_class($request));
        }
    }
}

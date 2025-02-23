<?php

declare(strict_types=1);

namespace SamboSearch\Client\Response\Mapper;

use SamboSearch\Client\Request\RequestInterface;
use SamboSearch\Client\Response\ResponseInterface;

interface ResponseMapperInterface
{
    /**
     * Maps the given request with the data to a response.
     */
    public function map(RequestInterface $request, array $data): ResponseInterface;
}

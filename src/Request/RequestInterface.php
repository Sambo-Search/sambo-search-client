<?php

declare(strict_types=1);

namespace SamboSearch\Client\Request;

interface RequestInterface
{
    /**
     * Gets the API route for the request. E.g. /search
     */
    public function getRoute(): string;

    /**
     * Gets the HTTP method for the request. E.g. GET, POST, etc.
     */
    public function getMethod(): string;

    /**
     * Gets all query parameters for the given request as raw values (not URL encoded).
     */
    public function getParams(): array;
}

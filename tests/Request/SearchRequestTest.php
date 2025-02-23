<?php

declare(strict_types=1);

namespace SamboSearchTest\Client\Request;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;
use SamboSearch\Client\Request\SearchRequest;

class SearchRequestTest extends TestCase
{
    public function testSearchRequestCanSetQuery(): void
    {
        $expectedQuery = 'foobar';

        $searchRequest = SearchRequest::getInstance();
        $searchRequest->setQuery($expectedQuery);

        $this->assertSame($expectedQuery, $searchRequest->getQuery());
        $this->assertSame($expectedQuery, $searchRequest->getParams()['query']);
    }

    public function testSearchRequestPaginationPageAndLimit(): void
    {
        $expectedPage = 69;
        $expectedLimit = 19;

        $searchRequest = SearchRequest::getInstance();
        $searchRequest->setPage($expectedPage);
        $searchRequest->setLimit($expectedLimit);

        $this->assertSame($expectedPage, $searchRequest->getPage());
        $this->assertSame($expectedLimit, $searchRequest->getLimit());
        $this->assertSame($expectedPage, $searchRequest->getParams()['page']);
        $this->assertSame($expectedLimit, $searchRequest->getParams()['limit']);
    }

    public function testFilterValueAsArray(): void
    {
        $expectedFilterName = 'color';
        $expectedValue = 'Red|Blue|Green|Yellow';
        $submittedValue = ['Red', 'Blue', 'Green', 'Yellow'];

        $searchRequest = SearchRequest::getInstance();
        $searchRequest->addFilter($expectedFilterName, $submittedValue);

        $this->assertSame($expectedValue, $searchRequest->getParams()[$expectedFilterName]);
    }

    public function testSearchRequestAddingFilters(): void
    {
        $expectedFilters = [
            'manufacturer' => 'Sambo-Search',
            'categories' => 'Dogs|Food|Wet Food',
            'price-' => '10.99',
            'price' => '35.00',
        ];

        $searchRequest = SearchRequest::getInstance();
        foreach ($expectedFilters as $identifier => $value) {
            $searchRequest->addFilter($identifier, $value);
        }

        $this->assertCount(4, $searchRequest->getFilters());
        $this->assertCount(5, $searchRequest->getParams());
        foreach ($expectedFilters as $identifier => $value) {
            $this->assertSame($value, $searchRequest->getParams()[$identifier]);
        }
    }

    public function testSearchRequestSortBy(): void
    {
        $expectedSortBy = 'created_at';

        $searchRequest = SearchRequest::getInstance();
        $searchRequest->setSortBy($expectedSortBy);

        $this->assertSame($expectedSortBy, $searchRequest->getSortBy());
        $this->assertSame($expectedSortBy, $searchRequest->getParams()['sort']);
    }

    public function testSearchRequestSortOrder(): void
    {
        $sortOrder = 'ASC';
        $expectedSortBy = 'relevance';
        $expectedSortByParams = '-relevance';

        $searchRequest = SearchRequest::getInstance();
        $searchRequest->setSortOrder($sortOrder);

        $this->assertSame($expectedSortBy, $searchRequest->getSortBy());
        $this->assertSame($expectedSortByParams, $searchRequest->getParams()['sort']);
    }

    public function testSearchRequestGroup(): void
    {
        $expectedGroup = 'bestGroup1234';

        $searchRequest = SearchRequest::getInstance();
        $searchRequest->setGroup($expectedGroup);

        $this->assertSame($expectedGroup, $searchRequest->getGroup());
        $this->assertSame($expectedGroup, $searchRequest->getParams()['group']);
    }

    public function testInitializeWithParams(): void
    {
        $expectedQuery = 'foobar';
        $expectedPage = 4;
        $expectedLimit = 10;

        $searchRequest = SearchRequest::getInstance($expectedQuery, $expectedPage, $expectedLimit);

        $this->assertSame($expectedQuery, $searchRequest->getQuery());
        $this->assertSame($expectedQuery, $searchRequest->getParams()['query']);
        $this->assertSame($expectedPage, $searchRequest->getPage());
        $this->assertSame($expectedPage, $searchRequest->getParams()['page']);
        $this->assertSame($expectedLimit, $searchRequest->getLimit());
        $this->assertSame($expectedLimit, $searchRequest->getParams()['limit']);
    }

    public function testFailsIfInvalidSortingOrderIsGiven(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid sort order value: invalid');

        $searchRequest = SearchRequest::getInstance();

        $searchRequest->setSortOrder('invalid');
    }
}

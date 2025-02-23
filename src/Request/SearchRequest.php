<?php

declare(strict_types=1);

namespace SamboSearch\Client\Request;

class SearchRequest implements RequestInterface
{
    private string $query = '';
    private ?int $page = null;
    private ?int $limit = null;

    /**
     * @var array<string, string>
     */
    private array $filters = [];

    private string $sortBy = 'relevance';
    private string $sortOrder = 'DESC';
    private ?string $group = null;

    public static function getInstance(
        string $query = '',
        ?int $page = null,
        ?int $limit = null
    ): static {
        $instance = new static();

        if ($query !== '') {
            $instance->setQuery($query);
        }
        if ($page !== null) {
            $instance->setPage($page);
        }
        if ($limit !== null) {
            $instance->setLimit($limit);
        }

        return $instance;
    }

    public function getRoute(): string
    {
        return '/search';
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getParams(): array
    {
        $params = [];

        foreach ($this->filters as $identifier => $value) {
            $params[$identifier] = $value;
        }

        if ($this->isQuerySet()) {
            $params['query'] = $this->query;
        }

        if ($this->isPageSet()) {
            $params['page'] = $this->page;
        }

        if ($this->isLimitSet()) {
            $params['limit'] = $this->limit;
        }

        if ($this->isGroupSet()) {
            $params['group'] = $this->group;
        }

        $params['sort'] = $this->getSortParam();

        return $params;
    }

    /**
     * Sets the users search query. Examples:
     *
     * ```
     * $searchRequest->setQuery('red sneakers'); // /api/v1/search?query=red%20sneakers
     * $searchRequest->setQuery('backpack'); // /api/v1/search?query=backpack
     * $searchRequest->setQuery('mssispellled'); // /api/v1/search?query=mssispellled
     * ```
     */
    public function setQuery(string $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Sets the current page. Use for pagination. Examples:
     *
     * ```
     * $searchRequest->setPage(5); // /api/v1/search?page=5
     * $searchRequest->setPage(3); // /api/v1/search?page=3
     * $searchRequest->setPage(17); // /api/v1/search?page=17
     * ```
     *
     * @see setLimit pagination page limit (products per page)
     */
    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Sets the pagination page limit (products per page). Examples:
     *
     * ```
     * $searchRequest->setLimit(24); // /api/v1/search?limit=24
     * $searchRequest->setLimit(10); // /api/v1/search?limit=10
     * $searchRequest->setLimit(6); // /api/v1/search?limit=6
     * ```
     *
     * @see setPage pagination page number (current page)
     */
    public function setLimit(?int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Selects a filter with the given identifier and value. Examples:
     *
     * ```
     * $searchRequest->addFilter(['manufacturer', 'Sambo-Search']); // /api/v1/search?manufacturer=Sambo-Search
     * $searchRequest->addFilter(['color', 'Green|Yellow']); // /api/v1/search?color=Green%7CYellow
     * $searchRequest->addFilter(['color', ['Green', 'Yellow']]); // same /api/v1/search?color=Green%7CYellow
     * $searchRequest->addFilter([
     *     'categories',
     *     'Dogs|Food|Wet Food'
     * ]); // /api/v1/search?categories=Dogs%7CFood%7CWet%20Food
     * $searchRequest->addFilter(['price-', '10.99']); // min price /api/v1/search?price-=10.99
     * $searchRequest->addFilter(['price', '35.00']); // max price /api/v1/search?price=35.00
     * ```
     */
    public function addFilter(string $identifier, string|array $values): static
    {
        $value = $values;
        if (is_array($values)) {
            $value = implode('|', $values);
        }

        $this->filters[$identifier] = $value;

        return $this;
    }

    /**
     * Sets the group that the requesting user is assigned to. Examples:
     *
     * ```
     * $searchRequest->setGroup('regular'); // /api/v1/search?group=regular
     * $searchRequest->setGroup('discountGroup'); // /api/v1/search?group=discountGroup
     * ```
     */
    public function setGroup(string $group): static
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Sets the sort column. Examples:
     *
     * ```
     * $searchRequest->setSort('relevance'); // default /api/v1/search?sort=relevance
     * $searchRequest->setSort('created_at'); // /api/v1/search?sort=created_at
     * $searchRequest->setSort('name'); // /api/v1/search?sort=name
     * ```
     *
     * @see setSortOrder
     */
    public function setSortBy(string $sortBy): static
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * Sets the sort order. Either ASC or DESC. Examples:
     *
     * ```
     * $searchRequest->setSortOrder('DESC'); // /api/v1/search?sort=relevance
     * $searchRequest->setSortOrder('ASC'); // /api/v1/search?sort=-relevance
     * ```
     * @see setSortBy
     */
    public function setSortOrder(string $order): static
    {
        if (!in_array($order, ['DESC', 'ASC'], true)) {
            throw new \InvalidArgumentException('Invalid sort order value: ' . $order);
        }

        $this->sortOrder = $order;

        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    /**
     * Used internally to build the proper Sambo-Search API sorting. See:
     *
     * ```php
     * // Default:
     * $searchRequest->setSort(['relevance']);
     * $searchRequest->setSortOrder(['DESC']);
     *
     * // result: ?sort=relevance
     *
     * // ASC example:
     * $searchRequest->setSort(['created_at']);
     * $searchRequest->setSortOrder(['ASC']);
     *
     * // result: ?sort=-relevance
     * ```
     */
    private function getSortParam(): string
    {
        $orderPrefix = $this->getSortOrder() === 'DESC' ? '' : '-';

        return $orderPrefix . $this->sortBy;
    }

    public function isQuerySet(): bool
    {
        return strlen(trim($this->query)) > 0;
    }

    public function isPageSet(): bool
    {
        return $this->page !== null && $this->page !== 1;
    }

    public function isLimitSet(): bool
    {
        return $this->limit !== null;
    }

    public function isGroupSet(): bool
    {
        return $this->group !== null;
    }
}

<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractListController extends AbstractDataController
{
    protected function extractFilter(ServerRequestInterface $request): array
    {
        $filter = $request->getQueryParams()['filter'] ?? [];

        return is_array($filter) ? $filter : [];
    }

    protected function extractLimit(ServerRequestInterface $request): int
    {
        return max(1, min(100, (int) Arr::get($request->getQueryParams(), 'page.limit', $this->limit ?? 20)));
    }

    protected function extractOffset(ServerRequestInterface $request): int
    {
        return max(0, (int) Arr::get($request->getQueryParams(), 'page.offset', 0));
    }

    protected function extractSort(ServerRequestInterface $request): array
    {
        $sort = $request->getQueryParams()['sort'] ?? null;
        if (! is_string($sort)) {
            return $this->sort ?? [];
        }
        $result = [];
        foreach (explode(',', $sort) as $field) {
            $name = ltrim($field, '-');
            if (in_array($name, $this->sortFields ?? [], true)) {
                $result[$name] = str_starts_with($field, '-') ? 'desc' : 'asc';
            }
        }

        return $result;
    }
}

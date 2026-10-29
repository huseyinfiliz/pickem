<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

/** Pagination metadata for the extension's custom list endpoints. */
class Document
{
    private array $meta = [];
    private array $links = [];

    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    public function addPaginationLinks(string $url, array $params, int $offset, int $limit, int $total): void
    {
        $link = function (int $start) use ($url, $params, $limit): string {
            $params['page'] = ['offset' => $start, 'limit' => $limit];

            return $url.'?'.http_build_query($params);
        };
        $this->links = ['first' => $link(0)];
        if ($offset > 0) {
            $this->links['prev'] = $link(max(0, $offset - $limit));
        }
        if ($offset + $limit < $total) {
            $this->links['next'] = $link($offset + $limit);
        }
    }

    public function toArray(): array
    {
        return array_filter(['meta' => $this->meta, 'links' => $this->links]);
    }
}

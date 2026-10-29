<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Api\Context;
use Flarum\Api\JsonApi;
use Flarum\Api\JsonApiResponse;
use Flarum\Api\Serializer;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** Shared response handling for Pick'em's custom, permission-checked endpoints. */
abstract class AbstractDataController implements RequestHandlerInterface
{
    public $include = [];

    abstract protected function data(ServerRequestInterface $request, Document $document);

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $document = new Document();
        $models = $this->data($request, $document);
        $api = resolve(JsonApi::class);
        $context = (new Context($api, $request))
            ->withCollection($api->getCollection($this->resource))
            ->withEndpoint(\Flarum\Api\Endpoint\Show::make());
        $serializer = new Serializer($context);
        $resource = $api->getResource($this->resource);
        $include = [];
        foreach ($this->include as $path) {
            Arr::set($include, $path, []);
        }
        $isList = $this instanceof AbstractListController;
        foreach ($isList ? $models : [$models] as $model) {
            $serializer->addPrimary($resource, $model, $include);
        }
        [$primary, $included] = $serializer->serialize();

        return new JsonApiResponse(array_merge([
            'data' => $isList ? $primary : ($primary[0] ?? null),
            'included' => $included,
        ], $document->toArray()), $this instanceof AbstractCreateController ? 201 : 200);
    }
}

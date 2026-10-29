<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractDeleteController implements RequestHandlerInterface
{
    abstract protected function delete(ServerRequestInterface $request);

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->delete($request);

        return new EmptyResponse(204);
    }
}

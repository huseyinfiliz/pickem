<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\SeasonResource;
use HuseyinFiliz\Pickem\Season;
use Psr\Http\Message\ServerRequestInterface;

class ListPublicSeasonsController extends AbstractListController
{
    public $resource = SeasonResource::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        // Use 'pickem.view' permission instead of 'pickem.manage'
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.view');

        return Season::orderBy('start_date', 'desc')->get();
    }
}

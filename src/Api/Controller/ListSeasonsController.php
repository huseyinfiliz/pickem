<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil; // YENİ
use HuseyinFiliz\Pickem\Api\Resource\SeasonResource;
use HuseyinFiliz\Pickem\Season;
use Psr\Http\Message\ServerRequestInterface;

class ListSeasonsController extends AbstractListController
{
    public $resource = SeasonResource::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        // YENİ: Admin yetkisi kontrolü eklendi
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        return Season::orderBy('start_date', 'desc')->get();
    }
}

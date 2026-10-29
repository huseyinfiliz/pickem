<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil; // YENİ: Import et
use HuseyinFiliz\Pickem\Api\Resource\TeamResource;
use HuseyinFiliz\Pickem\Team;
use Psr\Http\Message\ServerRequestInterface;

class ListTeamsController extends AbstractListController
{
    public $resource = TeamResource::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        // YENİ: Admin yetkisi kontrolü eklendi
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        return Team::all();
    }
}

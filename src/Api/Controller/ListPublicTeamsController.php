<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\TeamResource;
use HuseyinFiliz\Pickem\Team;
use Psr\Http\Message\ServerRequestInterface;

class ListPublicTeamsController extends AbstractListController
{
    public $resource = TeamResource::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        // Use 'pickem.view' permission instead of 'pickem.manage'
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.view');

        return Team::all();
    }
}

<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Team;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class DeleteTeamController extends AbstractDeleteController
{
    public function __construct(protected Translator $translator)
    {
    }

    protected function delete(ServerRequestInterface $request): void
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertCan('pickem.manage');

        $id = Arr::get($request->getQueryParams(), 'id');
        $team = Team::findOrFail($id);

        if ($team->homeEvents()->exists() || $team->awayEvents()->exists()) {
            throw new ValidationException([/* GÜNCELLENDİ: lib.validation.errors.team_in_use -> lib.messages.in_use */ 'message' => $this->translator->trans('huseyinfiliz-pickem.lib.messages.in_use')]);
        }

        $team->delete();
    }
}

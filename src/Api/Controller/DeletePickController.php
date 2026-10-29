<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Job\RecalculateUserScoreJob;
use HuseyinFiliz\Pickem\Pick;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Translation\Translator; // Translator eklendi
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class DeletePickController extends AbstractDeleteController
{
    public function __construct(protected Dispatcher $bus, protected Translator $translator)
    {
    }

    protected function delete(ServerRequestInterface $request): void
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');

        $pick = Pick::findOrFail($id);

        // 1. Önce zaman aşımı kontrolü yap (Özel hata mesajı için)
        if (! $pick->event->canPick()) {
            throw new ValidationException(['message' => $this->translator->trans('huseyinfiliz-pickem.lib.messages.invalid_outcome')]);
        }

        // 2. Sonra yetki kontrolü yap (Sahiplik vb.)
        // Policy de canPick kontrolü yapıyor ama yukarıdaki blok sayesinde
        // süre dolmuşsa kod buraya gelmeden exception fırlatacak.
        $actor->assertCan('delete', $pick);

        // Silme işleminden önce user ID'yi al
        $userId = $pick->user_id;

        $pick->delete();

        // Puanı yeniden hesapla
        $this->bus->dispatch(
            new RecalculateUserScoreJob($userId)
        );
    }
}

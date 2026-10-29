<?php

namespace HuseyinFiliz\Pickem\Api\Controller;

use Flarum\Foundation\ValidationException;
use Flarum\Http\RequestUtil;
use HuseyinFiliz\Pickem\Api\Resource\PickResource;
use HuseyinFiliz\Pickem\Event;
use HuseyinFiliz\Pickem\Job\RecalculateUserScoreJob;
use HuseyinFiliz\Pickem\Pick;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

class UpdatePickController extends AbstractShowController
{
    public $resource = PickResource::class;
    public $include = ['event', 'event.homeTeam', 'event.awayTeam', 'user'];

    public function __construct(protected Dispatcher $bus, protected Translator $translator)
    {
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $id = Arr::get($request->getQueryParams(), 'id');

        $pick = Pick::with('event')->findOrFail($id);

        // 1. Önce Zaman Aşımı Kontrolü (Cutoff)
        // Policy'den önce burayı kontrol ediyoruz ki özel hata mesajımız dönsün.
        if (! $pick->event->canPick()) {
            // GÜNCELLENDİ: Özel 'cutoff_passed' yerine genel 'invalid_outcome' kullanıyoruz.
            throw new ValidationException(['message' => $this->translator->trans('huseyinfiliz-pickem.lib.messages.invalid_outcome')]);
        }

        // 2. Sonra Yetki Kontrolü (Kullanıcı kendi tahminini mi düzenliyor?)
        $actor->assertCan('edit', $pick);

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);
        $selectedOutcome = Arr::get($data, 'selectedOutcome');

        if (! $selectedOutcome) {
            return $pick;
        }

        // Beraberlik kontrolü
        if (Event::RESULT_DRAW === $selectedOutcome && ! $pick->event->allow_draw) {
            throw new ValidationException(['message' => $this->translator->trans('huseyinfiliz-pickem.lib.messages.invalid_outcome')]);
        }

        // Geçerli sonuç tipi kontrolü
        $validOutcomes = [Event::RESULT_HOME, Event::RESULT_AWAY, Event::RESULT_DRAW];
        if (! in_array($selectedOutcome, $validOutcomes)) {
            throw new ValidationException(['message' => $this->translator->trans('huseyinfiliz-pickem.lib.messages.invalid_outcome')]);
        }

        $recalculateScore = false;

        if ($pick->selected_outcome !== $selectedOutcome) {
            $pick->selected_outcome = $selectedOutcome;
            $pick->is_correct = null;

            // Normalde canPick() kontrolü süresi geçmiş maçları engeller ama
            // yine de ekstra güvenlik olarak kalabilir.
            if ($pick->event->isFinished()) {
                $recalculateScore = true;
            }
        }

        $pick->save();

        if ($recalculateScore) {
            $this->bus->dispatch(
                new RecalculateUserScoreJob($pick->user_id)
            );
        }

        $pick->load(['event.homeTeam', 'event.awayTeam', 'event.week', 'user']);

        return $pick;
    }
}

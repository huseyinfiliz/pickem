<?php

namespace HuseyinFiliz\Pickem\Job;

use Flarum\Notification\NotificationSyncer;
use Flarum\Queue\AbstractJob;
use HuseyinFiliz\Pickem\Event;
use HuseyinFiliz\Pickem\Notification\EventResultBlueprint;
use HuseyinFiliz\Pickem\Pick;
use HuseyinFiliz\Pickem\PickemScoringService;

class ProcessEventResultsJob extends AbstractJob
{
    public function __construct(protected int $eventId)
    {
    }

    public function handle(PickemScoringService $scoringService, NotificationSyncer $notifications)
    {
        $event = Event::find($this->eventId);

        if (! $event || ! $event->isFinished()) {
            return;
        }

        // 1. Puanları yeniden hesapla (Bu, PickemScoringService'ten gelen mantık)
        $scoringService->updateScoresForEvent($event);

        // 2. Bildirimleri gönder (Bu, EnterEventResultController'dan taşınan mantık)
        $picks = Pick::where('event_id', $event->id)
            ->with('user')
            ->get();

        $users = $picks->pluck('user')->filter();

        if ($users->isEmpty()) {
            return;
        }

        $notifications->sync(
            new EventResultBlueprint($event),
            $users->all()
        );
    }
}

<?php

namespace HuseyinFiliz\Pickem\Listener;

use HuseyinFiliz\Pickem\Event;
use HuseyinFiliz\Pickem\Job\ProcessEventResultsJob;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdateUserScoresListener
{
    public function __construct(protected Dispatcher $bus)
    {
    }

    public function handle(Event $event): void
    {
        if ($event->isFinished() && null !== $event->result
            && $event->wasChanged(['result', 'home_score', 'away_score', 'status'])) {
            $this->bus->dispatch(new ProcessEventResultsJob($event->id));
        }
    }
}

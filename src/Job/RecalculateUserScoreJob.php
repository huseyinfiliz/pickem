<?php

namespace HuseyinFiliz\Pickem\Job;

use Flarum\Queue\AbstractJob;
use Flarum\User\User; // Import the scoring service
use HuseyinFiliz\Pickem\PickemScoringService;

// Unused imports removed
// use HuseyinFiliz\Pickem\Pick;
// use HuseyinFiliz\Pickem\UserScore;
// use Illuminate\Database\DatabaseManager;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Support\Carbon;

class RecalculateUserScoreJob extends AbstractJob
{
    public function __construct(protected int $userId)
    {
    }

    /**
     * Inject the service directly into the handle method.
     * Flarum's Queue system will automatically resolve this dependency.
     */
    public function handle(PickemScoringService $scoringService)
    {
        if (null === User::find($this->userId)) {
            // If user doesn't exist, stop the job.
            return;
        }

        // The old logic (delete all scores, then create a new one) is removed.
        // We now call the centralized service, which performs a safe "upsert"
        // (update or create). This ensures logical consistency across the app.
        $scoringService->recalculateUserScore($this->userId);
    }
}

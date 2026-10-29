<?php

namespace HuseyinFiliz\Pickem\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use HuseyinFiliz\Pickem\Pick;

class PickPolicy extends AbstractPolicy
{
    /**
     * @return bool|void
     */
    public function can(User $actor, string $ability)
    {
        if ($actor->can('pickem.makePicks') && 'create' === $ability) {
            return true;
        }

        if ($actor->can('pickem.makePicks') && 'view' === $ability) {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function edit(User $actor, Pick $pick)
    {
        // Check if the user owns the pick
        // AND the event is still in a 'pickable' state
        return $actor->id === $pick->user_id && $pick->event->canPick();
    }

    /**
     * @return bool
     */
    public function delete(User $actor, Pick $pick)
    {
        // Deletion logic is the same as editing
        return $this->edit($actor, $pick);
    }

    /**
     * @return bool
     */
    public function view(User $actor, Pick $pick)
    {
        return $actor->id === $pick->user_id || $actor->can('pickem.manage');
    }
}

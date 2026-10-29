<?php

namespace HuseyinFiliz\Pickem\Access;

use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;
use HuseyinFiliz\Pickem\Event;

class EventPolicy extends AbstractPolicy
{
    /**
     * @return bool|void
     */
    public function can(User $actor, string $ability)
    {
        // 'pickem.view' iznine sahip olanlar
        // 'view' (görüntüleme) yetkisine sahiptir.
        if ($actor->can('pickem.view') && 'view' === $ability) {
            return true;
        }
    }

    /**
     * This will be called by the Serializer for each event.
     * @return bool
     */
    public function view(User $actor, Event $event)
    {
        return $actor->can('pickem.view');
    }
}

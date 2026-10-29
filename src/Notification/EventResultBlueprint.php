<?php

namespace HuseyinFiliz\Pickem\Notification;

use Flarum\Notification\AlertableInterface;
use Flarum\Notification\Blueprint\BlueprintInterface;
use HuseyinFiliz\Pickem\Event;

class EventResultBlueprint implements BlueprintInterface, AlertableInterface
{
    public function __construct(public Event $event)
    {
    }

    public function getSubject(): ?\Flarum\Database\AbstractModel
    {
        return $this->event;
    }

    public function getSender()
    {
        return null;
    }

    public function getFromUser(): ?\Flarum\User\User
    {
        return null;
    }

    public function getData(): mixed
    {
        // GÜNCELLENDİ: Sabit 'Home'/'Away' yerine translator kullanımı.
        // Not: Bu metot email template'lerinde de kullanıldığı için burada çevirmek mantıklıdır.
        $translator = resolve('translator');

        return [
            'eventId' => $this->event->id,
            'homeTeam' => $this->event->homeTeam ? $this->event->homeTeam->name : $translator->trans('huseyinfiliz-pickem.lib.common.home'),
            'awayTeam' => $this->event->awayTeam ? $this->event->awayTeam->name : $translator->trans('huseyinfiliz-pickem.lib.common.away'),
            'result' => $this->event->result,
            'homeScore' => $this->event->home_score,
            'awayScore' => $this->event->away_score,
        ];
    }

    public static function getType(): string
    {
        return 'pickem_event_result';
    }

    public static function getSubjectModel(): string
    {
        return Event::class;
    }
}

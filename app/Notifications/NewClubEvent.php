<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewClubEvent extends Notification
{
    use Queueable;

    public function __construct(public Event $event)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id'   => $this->event->id,
            'event_name' => $this->event->name,
            'club_name'  => $this->event->club->name,
            'message'    => "{$this->event->club->name} just posted a new event: \"{$this->event->name}\".",
        ];
    }
}

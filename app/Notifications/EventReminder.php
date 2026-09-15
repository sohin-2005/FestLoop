<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminder extends Notification
{
    use Queueable;

    public function __construct(public Event $event)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id'   => $this->event->id,
            'event_name' => $this->event->name,
            'kind'       => 'reminder',
            'message'    => "Reminder: \"{$this->event->name}\" starts {$this->event->start_time->diffForHumans()} at {$this->event->location}.",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Tomorrow: {$this->event->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Just a reminder that \"{$this->event->name}\" is coming up.")
            ->line('When: '.$this->event->start_time->isoFormat('dddd, MMM D · h:mm A'))
            ->line('Where: '.$this->event->location)
            ->action('View event details', route('events.show', $this->event))
            ->line('See you there!');
    }
}

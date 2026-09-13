<?php

namespace App\Notifications;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RegistrationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Registration $registration)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $event = $this->registration->event;

        return [
            'event_id'    => $event->id,
            'event_name'  => $event->name,
            'status'      => $this->registration->status,
            'message'     => match ($this->registration->status) {
                'registered' => "A spot opened up — you're now registered for \"{$event->name}\".",
                'pending'    => "Your registration for \"{$event->name}\" is awaiting approval.",
                default      => "Update on your registration for \"{$event->name}\".",
            },
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->registration->event;

        return (new MailMessage)
            ->subject("Update on your registration — {$event->name}")
            ->line("Good news! A spot opened up and you're now {$this->registration->label} for \"{$event->name}\".")
            ->action('View event', route('events.show', $event))
            ->line('See you there!');
    }
}

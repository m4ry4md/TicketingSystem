<?php

namespace App\Notifications;

use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminReplied extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ticket $ticket, public Reply $reply)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/tickets/' . $this->ticket->uuid); // Assuming a user-facing URL
        return (new MailMessage)
            ->subject('A reply was added to your ticket: ' . $this->ticket->title)
            ->line('A support agent has replied to your ticket.')
            ->line('Reply: ' . $this->reply->message)
            ->action('View Ticket', $url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

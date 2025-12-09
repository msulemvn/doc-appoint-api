<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MessageSentNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'file' => $this->message->file,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $message = $this->message->load('sender');

        return new BroadcastMessage([
            'message' => $message->toArray(),
        ]);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

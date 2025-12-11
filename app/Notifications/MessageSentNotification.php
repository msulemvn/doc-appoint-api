<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MessageSentNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message)
    {
        $this->onQueue('notification');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $messageText = $this->message->content
            ?: ($this->message->file ? '📎 Sent an attachment' : 'New message');

        return [
            'message_id' => $this->message->id,
            'chat_uuid' => $this->message->chat->uuid,
            'sender_id' => $this->message->user_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'file' => $this->message->file,
            'message' => sprintf('New message from %s: %s', $this->message->sender->name, $messageText),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

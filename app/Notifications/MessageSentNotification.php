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
        $notificationId = $notifiable->notifications()
            ->where('type', static::class)
            ->where('data->message_id', $this->message->id)
            ->latest()
            ->first()?->id ?? uniqid();

        return new BroadcastMessage([
            'id' => $notificationId,
            'type' => static::class,
            'data' => [
                'message_id' => $this->message->id,
                'chat_id' => $this->message->chat_id,
                'sender_id' => $this->message->user_id,
                'sender_name' => $this->message->sender->name,
                'content' => $this->message->content,
                'file' => $this->message->file,
            ],
            'read_at' => null,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ]);
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

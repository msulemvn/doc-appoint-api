<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Models\User;
use App\Notifications\MessageSentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMessageSentNotification implements ShouldQueue
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message->load('chat', 'sender');
        $chat = $message->chat;

        $senderId = $message->user_id;
        $recipientId = $chat->user1_id === $senderId ? $chat->user2_id : $chat->user1_id;

        $recipient = User::find($recipientId);

        if ($recipient) {
            $recipient->notify(new MessageSentNotification($message));
        }
    }
}

<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\MessageSent;
use App\Notifications\MessageSentNotification;

class SendMessageSentNotification
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $chat = $message->chat;

        $senderId = $message->user_id;
        $recipientId = $chat->user1_id === $senderId ? $chat->user2_id : $chat->user1_id;

        $recipient = User::find($recipientId);

        if ($recipient) {
            $recipient->notify(new MessageSentNotification($message));
        }
    }
}

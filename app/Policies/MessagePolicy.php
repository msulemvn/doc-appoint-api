<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    /**
     * Determine whether the user can mark the message as read.
     */
    public function markAsRead(User $user, Message $message): bool
    {
        return ($user->id === $message->chat->user1_id || $user->id === $message->chat->user2_id)
            && $user->id !== $message->user_id;
    }
}

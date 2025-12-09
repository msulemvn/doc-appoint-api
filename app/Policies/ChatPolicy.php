<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user1_id || $user->id === $chat->user2_id;
    }

    /**
     * Determine whether the user can send messages to the chat.
     */
    public function sendMessages(User $user, Chat $chat): bool
    {
        return $user->id === $chat->user1_id || $user->id === $chat->user2_id;
    }
}

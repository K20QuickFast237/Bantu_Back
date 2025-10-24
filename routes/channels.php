<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversations.{conversationId}', function ($user, $conversationId) {
    return \App\Models\Conversation::where('id', $conversationId)
        ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
        ->exists();
});


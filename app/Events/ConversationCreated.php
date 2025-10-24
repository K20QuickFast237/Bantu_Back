<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreated
{
    use Dispatchable, SerializesModels;

    public $conversation;

    /**
     * Crée une nouvelle instance d'événement.
     *
     * @param \App\Models\Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * Définir le canal de diffusion.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('conversation.' . $this->conversation->id);
    }

    /**
     * Définir le nom de l'événement.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'conversation.created';
    }
}

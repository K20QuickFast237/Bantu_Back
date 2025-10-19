<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('attachments', 'sender');
        
        // Ajout d'un log pour vérifier l'événement
        \Log::info('Événement MessageSent créé', [
            'message_id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversations.' . $this->message->conversation_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}

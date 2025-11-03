<?php
namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels; 

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message; //->load('attachments', 'sender');
        
        // Ajout d'un log pour vérifier l'événement
        \Log::info('Événement MessageSent créé', [
            // 'message_id' => $this->message->id,
            // 'conversation_id' => $this->message->conversation_id,
            'time' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversations.' . $this->message->conversation_id);
    }

    // public function broadcastWith()
    // {
    //     return [
    //         'message' => (array)$this->message,
    //     ];
    // }

    // public function broadcastAs()
    // {
    //     return 'message.sent';
    // }
}

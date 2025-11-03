<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Conversation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function store(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'nullable|string',
            'attachments.*' => 'file|max:10240', // chaque fichier max 10MB
        ]);

        $conversation = Conversation::with('participants')->findOrFail($conversationId);

        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->participants->contains('id', auth()->id())) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        DB::beginTransaction();
        try {
            // Créer le message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'content' => $request->content,
                'is_sent' => true,
            ]);

            // Upload des fichiers si présents
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');

                // S'assurer que $files est un tableau (pour 1 ou plusieurs fichiers)
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    $path = $file->store('messages', 'public'); // stockage dans storage/app/public/messages
                    MessageAttachment::create([
                        'message_id' => $message->id,
                        'file_url' => $path,
                    ]);
                }
            }

            DB::commit();

            // Log avant l'émission de l'événement
            Log::info('Émission de l\'événement MessageSent', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'content' => $request->content,
            ]);

            // Émettre l'événement après l'enregistrement complet du message
            // event(new \App\Events\MessageSent($message));
            SendMessage::dispatch($message);

            // Retourner le message avec ses attachments
            return response()->json($message->load('attachments'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Envoi du message échoué', 'details' => $e->getMessage()], 500);
        }
    }

    // Marquer un message comme lu
    public function markAsRead(Message $message)
    {
        $conversation = $message->conversation;

        // Vérifier que l'utilisateur fait partie de la conversation et n'est pas l'expéditeur
        if (!$conversation->participants->contains('user_id', auth()->id()) || $message->sender_id == auth()->id()) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        if ($message->read_at === null) {
            $message->update(['read_at' => now()]);

            // Diffuser l'événement de message marqué comme lu
            event(new \App\Events\MessageRead($message));
        }

        return response()->json(['status' => 'read']);
    }

    // Modifier un message (max 10 min après envoi)
    public function update(Request $request, Message $message)
    {
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($message->created_at->diffInMinutes(now()) > 10) {
            return response()->json(['error' => 'Délai expiré'], 403);
        }

        $request->validate([
            'content' => 'nullable|string',
        ]);

        $message->update([
            'content' => $request->content,
            'edited_at' => now(),
        ]);

        // Diffuser l'événement de message mis à jour
        event(new \App\Events\MessageUpdated($message));

        return response()->json($message->load('attachments'));
    }

    // Supprimer un message (max 30 min après envoi)
    public function destroy(Message $message)
    {
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($message->created_at->diffInMinutes(now()) > 30) {
            return response()->json(['error' => 'Délai expiré'], 403);
        }

        $message->update(['deleted_at' => now()]);

        // Diffuser l'événement de message supprimé
        event(new \App\Events\MessageDeleted($message));

        return response()->json(['status' => 'deleted']);
    }
}
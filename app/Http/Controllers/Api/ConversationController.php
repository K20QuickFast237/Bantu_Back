<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Events\ConversationCreated;

class ConversationController extends Controller
{
    // Lister toutes les conversations d'un utilisateur
    public function index()
    {
        $userId = auth()->id();

        $conversations = Conversation::whereHas('participants', fn($q) => $q->where('user_id', $userId))
            ->with(['participants', 'messages' => fn($q) => $q->latest()->first()])
            ->get();

        return response()->json($conversations);
    }

    // Créer une conversation privée entre 2 utilisateurs
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // l'autre utilisateur
        ]);

        $userId = auth()->id();
        $otherUserId = $request->user_id;

        // Vérifier si une conversation entre ces 2 utilisateurs existe déjà
        $conversation = Conversation::whereHas('participants', fn($q) => $q->where('users.id', $userId))
            ->whereHas('participants', fn($q) => $q->where('users.id', $otherUserId))
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(); // pas de title

            $conversation->participants()->attach([
                $userId => ['joined_at' => now()],
                $otherUserId => ['joined_at' => now()],
            ]);

            // Diffuser l'événement que la conversation a été créée
            event(new \App\Events\ConversationCreated($conversation));
        }

        return response()->json($conversation->load('participants'));
    }

    // Afficher une conversation et tous ses messages
    public function show($id)
    {
        $conversation = Conversation::with(['participants', 'messages.attachments'])
            ->findOrFail($id);

        // Vérifier que l'utilisateur fait partie de la conversation
        $userId = auth()->id();
        if (!$conversation->participants->contains('id', $userId)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        // Marquer automatiquement les messages non lus destinés à l'utilisateur
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($conversation);
    }

    /**
     * Créer une conversation avec un freelancer (en utilisant freelancer_id)
     */
    public function createWithFreelancer(Request $request)
    {
        $request->validate([
            'freelancer_id' => 'required|exists:freelancers,id',
        ]);

        $user = auth()->user();
        $freelancer = \App\Models\Freelancer::with('user')->findOrFail($request->freelancer_id);

        if (!$freelancer->user) {
            return response()->json(['error' => 'Le freelancer n\'a pas de compte utilisateur associé'], 404);
        }

        $userId = $user->id;
        $freelancerUserId = $freelancer->user->id;

        // Vérifier si une conversation entre ces 2 utilisateurs existe déjà
        $conversation = Conversation::whereHas('participants', fn($q) => $q->where('users.id', $userId))
            ->whereHas('participants', fn($q) => $q->where('users.id', $freelancerUserId))
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create();

            $conversation->participants()->attach([
                $userId => ['joined_at' => now()],
                $freelancerUserId => ['joined_at' => now()],
            ]);

            event(new \App\Events\ConversationCreated($conversation));
        }

        return response()->json($conversation->load('participants'));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cv;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    // Liste des CVs de l'utilisateur connecté
    public function index()
    {
        $user = Auth::user();
        $particulier = $user->particulier;

        return response()->json($particulier->cvs);
    }

    // Ajouter un CV
    public function store(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'titre' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $particulier = $user->particulier;

        $path = $request->file('fichier')->store('cvs', 'public');

        $cv = $particulier->cvs()->create([
            'titre' => $request->titre,
            'fichier' => $path,
        ]);

        return response()->json([
            'message' => 'CV ajouté avec succès',
            'data' => $cv,
        ], 201);
    }

    // 🆕 Mettre à jour un CV
    public function update(Request $request, Cv $cv)
    {
        $user = Auth::user();

        // Vérifie que le CV appartient bien à l'utilisateur connecté
        if ($cv->particulier->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'titre' => 'nullable|string|max:255',
            'fichier' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = [];

        // Mise à jour du titre
        if ($request->filled('titre')) {
            $data['titre'] = $request->titre;
        }

        // Mise à jour du fichier (en supprimant l'ancien)
        if ($request->hasFile('fichier')) {
            if ($cv->fichier && Storage::disk('public')->exists($cv->fichier)) {
                Storage::disk('public')->delete($cv->fichier);
            }

            $data['fichier'] = $request->file('fichier')->store('cvs', 'public');
        }

        $cv->update($data);

        return response()->json([
            'message' => 'CV mis à jour avec succès',
            'data' => $cv,
        ]);
    }

    // Supprimer un CV
    public function destroy(Cv $cv)
    {
        $user = Auth::user();

        if ($cv->particulier->user_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $cv->delete();

        return response()->json(['message' => 'CV supprimé avec succès']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OffreEmploi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class FavoriController extends Controller
{
    // Ajouter une offre en favori
    public function add(Request $request)
    {
        $request->validate([
            'offre_emploi_id' => 'required|exists:offre_emplois,id',
        ]);

        $user = Auth::user();

        // Vérifier si déjà en favoris
        if ($user->favoris()->where('offre_emploi_id', $request->offre_emploi_id)->exists()) {
            return response()->json([
                'message' => 'Cette offre est déjà dans vos favoris.'
            ], Response::HTTP_CONFLICT);
        }

        $user->favoris()->attach($request->offre_emploi_id);

        return response()->json([
            'message' => 'Offre ajoutée aux favoris avec succès.'
        ], Response::HTTP_CREATED);
    }

    // Retirer une offre des favoris
    public function remove(Request $request)
    {
        $request->validate([
            'offre_emploi_id' => 'required|exists:offre_emplois,id',
        ]);

        $user = Auth::user();

        $user->favoris()->detach($request->offre_emploi_id);

        return response()->json([
            'message' => 'Offre retirée des favoris avec succès.'
        ], Response::HTTP_OK);
    }

    // Lister les favoris de l'utilisateur connecté
    public function list()
    {
        $user = Auth::user();

        $favoris = $user->favoris()->with('employeur')->get();

        return response()->json([
            'count' => $favoris->count(),
            'data' => $favoris,
        ], Response::HTTP_OK);
    }
}

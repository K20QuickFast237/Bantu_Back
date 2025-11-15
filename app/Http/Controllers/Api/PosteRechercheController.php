<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosteRecherche;
use App\Http\Requests\StorePosteRechercheRequest;
use App\Http\Requests\UpdatePosteRechercheRequest;
use Illuminate\Support\Facades\Auth;

class PosteRechercheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PosteRecherche::where('user_id', Auth::user()->id)->get();
        return response()->json([
            "message" => $data ? "Détails de poste recherché." : "Aucun détail de poste recherché trouvé",
            "data" => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePosteRechercheRequest $request)
    {
        $user = Auth::user();
        if (PosteRecherche::where('user_id', $user->id)) {
            return response()->json(['message' => "Vous avez déjà créé des détails de poste recherché."], 400);
        }
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['skills'] = json_encode($data['skills']);
        $data['localisations'] = json_encode($data['localisations']);
        $data['type_contrats'] = json_encode($data['type_contrats']);

        $data = PosteRecherche::create($data);
        return response()->json([
            'message' => "Détails de poste recherche enregistrés.",
            'poste' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePosteRechercheRequest $request, PosteRecherche $posteRecherche)
    {
        if ($posteRecherche->user_id != Auth::user()->id) {
            return response()->json(['message' => "Unauthorized. Vous n'êtes pas auteur de ces détails de poste"], 403);
        }
        $data = $request->validated();
        $posteRecherche->update($data);
        return response()->json([
            'message' => "Détails de poste recherché mis à jour.",
            'data' => $posteRecherche,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosteRecherche $posteRecherche)
    {
        if ($posteRecherche->user_id != Auth::user()->id) {
            return response()->json(['message' => "Unauthorized. Vous n'êtes pas auteur de ces détails de poste"], 403);
        }
        $posteRecherche->delete();
        return response()->json(['message' => 'Détails de poste recherché supprimés.']);
    }
}

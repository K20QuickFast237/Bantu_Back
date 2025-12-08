<?php

namespace App\Http\Controllers\Produit;

use App\Http\Controllers\Controller;
use App\Models\CategorieProduit;
use App\Http\Resources\Produit\CategorieProduitResource;
use Illuminate\Http\Request;

class CategorieProduitController extends Controller
{
    public function index()
    {
        return CategorieProduitResource::collection(CategorieProduit::all());
    }

    public function show($id)
    {
        $categorie = CategorieProduit::findOrFail($id);
        return new CategorieProduitResource($categorie);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'statut' => 'sometimes|required|in:active,inactive',
        ]);

        $categorie = CategorieProduit::create($data);
        return new CategorieProduitResource($categorie);
    }

    public function update(Request $request, $id)
    {
        $categorie = CategorieProduit::findOrFail($id);

        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'statut' => 'sometimes|in:active,inactive',
        ]);
        
        $categorie->update($data);
        return new CategorieProduitResource($categorie);
    }

    public function destroy($id)
    {
        $categorie = CategorieProduit::findOrFail($id);
        $categorie->delete();
        return response()->json(['message' => 'Catégorie supprimée avec succès.']);
    }
}

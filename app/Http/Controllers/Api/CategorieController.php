<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    // Créer une nouvelle catégorie
    public function store(StoreCategorieRequest $request)
    {
        $categorie = Categorie::create([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Catégorie créée avec succès.',
            'data' => $categorie,
        ], Response::HTTP_CREATED);
    }

    // Modifier une catégorie existante
    public function update(UpdateCategorieRequest $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        $categorie->update([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès.',
            'data' => $categorie,
        ], Response::HTTP_OK);
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès.'
        ], Response::HTTP_OK);
    }

    // Lister toutes les catégories
    public function index()
    {
        $categories = Categorie::withCount('offres')->get();

        return response()->json([
            'count' => $categories->count(),
            'data' => $categories,
        ], Response::HTTP_OK);
    }

    // Voir une seule catégorie et ses offres
    public function show($id)
    {
        $categorie = Categorie::with('offres')->findOrFail($id);

        return response()->json([
            'data' => $categorie,
        ], Response::HTTP_OK);
    }
}

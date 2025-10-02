<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // ===============================
    // CÔTÉ ADMIN
    // ===============================

    // Lister tous les tags
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

    // Créer un tag
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:tags,name',
            'slug' => 'required|string|unique:tags,slug',
        ]);

        $tag = Tag::create($request->only(['name','slug']));
        return response()->json($tag, 201);
    }

    // Modifier un tag
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'sometimes|string|unique:tags,name,' . $tag->id,
            'slug' => 'sometimes|string|unique:tags,slug,' . $tag->id,
        ]);

        $tag->update($request->only(['name','slug']));
        return response()->json($tag);
    }

    // Supprimer un tag
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['message' => 'Tag deleted successfully']);
    }

    // ===============================
    // CÔTÉ CLIENT / VENDEUR
    // ===============================
    // Listage public
    public function publicIndex()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }
}

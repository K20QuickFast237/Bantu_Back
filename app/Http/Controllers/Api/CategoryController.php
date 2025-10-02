<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // ===============================
    // CÔTÉ ADMIN
    // ===============================

    // Lister toutes les catégories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Créer une nouvelle catégorie
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);

        $category = Category::create($request->only(['name', 'slug']));

        return response()->json($category, 201);
    }

    // Modifier une catégorie
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        $category->update($request->only(['name', 'slug']));

        return response()->json($category);
    }

    // Supprimer une catégorie
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    // ===============================
    // CÔTÉ CLIENT / VENDEUR
    // ===============================
    // Listage public des catégories
    public function publicIndex()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ===============================
    // CÔTÉ VENDEUR
    // ===============================

    // Créer un produit
    public function store(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'image_product' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Gestion de l'image
        $imagePath = null;
        if ($request->hasFile('image_product')) {
            $imagePath = $request->file('image_product')->store('products', 'public');
        }

        // Création du produit (sans user_id)
        $product = Product::create([
            'shop_id' => $request->shop_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'image_product' => $imagePath,
            'category_id' => $request->category_id,
            'status' => 'active',
        ]);

        // Assigner les tags
        if ($request->tags) {
            $product->tags()->sync($request->tags);
        }

        return response()->json($product, 201);
    }

    // Modifier un produit
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric',
            'discount_price' => 'nullable|numeric',
            'stock' => 'sometimes|integer',
            'image_product' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $data = $request->only(['name','description','price','discount_price','stock']);

        // Gestion de l'image : suppression ancienne image si nécessaire
        if ($request->hasFile('image_product')) {
            if ($product->image_product && Storage::disk('public')->exists($product->image_product)) {
                Storage::disk('public')->delete($product->image_product);
            }
            $data['image_product'] = $request->file('image_product')->store('products', 'public');
        }

        $product->update($data);

        if ($request->tags) {
            $product->tags()->sync($request->tags);
        }

        return response()->json($product);
    }

    
    // Voir les produits de sa boutique
    public function myProducts($shop_id)
    {
        $products = Product::where('shop_id', $shop_id)->get();
        return response()->json($products);
    }

    

    // ===============================
    // CÔTÉ ADMIN
    // ===============================

    // Lister tous les produits
    public function index()
    {
        return response()->json(Product::all());
    }

    // Supprimer un produit (admin)
    public function destroy(Product $product)
    {
        // Supprimer l'image si existante
        if ($product->image_product && Storage::disk('public')->exists($product->image_product)) {
            Storage::disk('public')->delete($product->image_product);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    // Activer / désactiver un produit (admin)
    public function toggleStatus(Product $product)
    {
        $product->status = ($product->status === 'active') ? 'disabled' : 'active';
        $product->save();

        return response()->json($product);
    }

    // ===============================
    // CÔTÉ CLIENT
    // ===============================

    // Voir tous les produits d’une boutique
    public function shopProducts($shop_id)
    {
        $products = Product::where('shop_id', $shop_id)->get();
        return response()->json($products);
    }
    public function filterProducts(Request $request)
{
    $query = Product::query()->where('status', 'active'); // seulement les produits actifs

    // Filtrer par catégorie
    if ($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filtrer par tags (tableau de tag_ids)
    if ($request->has('tag_ids')) {
        $tagIds = $request->tag_ids; // peut être un array ou une chaîne CSV
        if (is_string($tagIds)) {
            $tagIds = explode(',', $tagIds);
        }

        $query->whereHas('tags', function ($q) use ($tagIds) {
            $q->whereIn('tags.id', $tagIds);
        });
    }

    $products = $query->with(['category', 'tags', 'shop'])->get();

    return response()->json($products);
}

}

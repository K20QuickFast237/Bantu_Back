<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ShopController extends Controller
{
    // Ajout du trait pour authorize
    use \Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    // ===============================
    // CÔTÉ VENDEUR
    // ===============================

    // Créer une boutique
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $shop = Shop::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'logo' => $request->logo,
            'description' => $request->description,
            'location' => $request->location,
            'status' => 'pending',
        ]);

        return response()->json($shop, 201);
    }

    // Modifier sa boutique
    public function update(Request $request, Shop $shop)
    {
        // Vérifie que l'utilisateur peut modifier cette boutique
        if (!Gate::allows('update-shop', $shop)) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'logo' => 'nullable|image',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $shop->update($request->only(['name', 'logo', 'description', 'location']));

        return response()->json($shop);
    }

    // Voir ses boutiques (vendeur)
    public function myShops()
    {
        $shops = Shop::where('user_id', Auth::id())->get();
        return response()->json($shops);
    }


    public function toggleStatus(Shop $shop)
{
    if (!Gate::allows('update-shop', $shop)) {
        return response()->json(['message' => 'This action is unauthorized.'], 403);
    }

    // Le vendeur peut maintenant passer de approved <-> disabled
    if (!in_array($shop->status, ['approved','disabled'])) {
        return response()->json(['message' => 'Only approved or disabled shops can be toggled by the seller.'], 403);
    }

    $shop->status = ($shop->status === 'approved') ? 'disabled' : 'approved';
    $shop->save();

    return response()->json($shop);
}



    // ===============================
    // CÔTÉ ADMIN
    // ===============================

    public function index()
    {
        $shops = Shop::all();
        return response()->json($shops);
    }

    public function approve(Shop $shop)
    {
        $shop->status = 'approved';
        $shop->save();
        return response()->json($shop);
    }

    public function reject(Shop $shop)
    {
        $shop->status = 'rejected';
        $shop->save();
        return response()->json($shop);
    }

    public function suspend(Shop $shop)
    {
        $shop->status = 'suspended';
        $shop->save();
        return response()->json($shop);
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();
        return response()->json(['message' => 'Shop deleted successfully.']);
    }

    // ===============================
    // CÔTÉ CLIENT
    // ===============================

   public function approvedShops()
{
    // Charger les boutiques approuvées avec leurs produits actifs et modes de livraison
    $shops = Shop::where('status', 'approved')
        ->with([
            'products' => function ($query) {
                $query->where('status', 'active'); // seulement les produits actifs
            },
            'deliveryMethods' => function ($query) {
                $query->select('delivery_methods.id', 'name'); // sélectionner uniquement les colonnes nécessaires
            }
        ])
        ->get();

    return response()->json($shops);
}


}
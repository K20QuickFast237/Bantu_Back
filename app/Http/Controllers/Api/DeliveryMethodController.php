<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\DeliveryMethod;
use Illuminate\Http\Request;

class DeliveryMethodController extends Controller
{
    // Lister les modes de livraison d’une boutique
    public function index($shopId)
    {
        $shop = Shop::with('deliveryMethods')->findOrFail($shopId);
        return response()->json($shop->deliveryMethods);
    }

    // Associer un mode de livraison à une boutique
    public function store(Request $request, $shopId)
    {
        $validated = $request->validate([
            'delivery_method_id' => 'required|exists:delivery_methods,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string',
        ]);

        $shop = Shop::findOrFail($shopId);

        $shop->deliveryMethods()->attach($validated['delivery_method_id'], [
            'price' => $validated['price'],
            'duration' => $validated['duration'] ?? null,
        ]);

        return response()->json(['message' => 'Mode de livraison ajouté à la boutique']);
    }

    // Supprimer un mode de livraison d’une boutique
    public function destroy($shopId, $deliveryMethodId)
    {
        $shop = Shop::findOrFail($shopId);
        $shop->deliveryMethods()->detach($deliveryMethodId);

        return response()->json(['message' => 'Mode de livraison retiré de la boutique']);
    }
}

<?php

namespace App\Http\Controllers\Acheteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favori;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Acheteur\FavoriResource;

class FavoriController extends Controller
{
    public function index()
    {
        $favoris = Auth::user()->acheteur->favoris()->with('produit')->get();
        return FavoriResource::collection($favoris);
        // return response()->json($favoris);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id' => 'required|exists:mkt_produits,id',
        ]);

        // $this->authorize('create', Favori::class);

        $favori = Favori::firstOrCreate([
            'acheteur_id' => Auth::user()->acheteur->id,
            'produit_id' => $data['produit_id']
        ]);

        return response()->json(['message' => 'Produit ajouté aux favoris', 'favori' => new FavoriResource($favori)]);
    }

    public function destroy($id)
    {
        try {
            $acheteurId = Auth::user()->acheteur->id;
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Vous n\'avez pas de favoris'], 404);
        }
        
        Favori::where('acheteur_id', $acheteurId)->where('produit_id', $id)->delete();
        return response()->json(['message' => 'Produit retiré des favoris']);
    }
}

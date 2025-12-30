<?php

namespace App\Http\Controllers\Acheteur;

use App\Http\Controllers\Controller;
use App\Http\Resources\Acheteur\PanierResource;
use Illuminate\Http\Request;
use App\Models\Panier;
use App\Http\Resources\Produit\ProduitResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use App\Models\CommandeProduit;
use App\Models\ProduitAttributValeur;
use App\Models\Produit;
use App\Models\CommandeProduitAttributValeur;
use Illuminate\Support\Facades\DB;

class PanierController extends Controller
{
    public function index()
    {
        $panier = Auth::user()->acheteur->paniers()->with('produit')->get();
        return PanierResource::collection($panier);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id' => 'required|exists:mkt_produits,id',
            'quantite' => 'required|integer|min:1',
            'produit_attribut_valeurs' => 'sometimes|array',
            'produit_attribut_valeurs.*' => 'sometimes|exists:mkt_produit_attribut_valeurs,id',
        ]);

        // Vérifier si ce produit est déjà dans le panier
        $existingPanierItem = Auth::user()->acheteur->paniers()->where('produit_id', $data['produit_id'])->first();
        DB::beginTransaction();
        if ($existingPanierItem) {
            $commande = Commande::find($existingPanierItem->commandeProduit->commande_id);
            CommandeProduit::where('id', $existingPanierItem->commande_produit_id)->delete();
            CommandeProduitAttributValeur::where('commande_produit_id', $existingPanierItem->commande_produit_id)->delete();
        } else {
            // Vérifier si une commande en attente existe pour l'acheteur
            $commande = Auth::user()->acheteur->commandes()->where('statut', 'en_attente')->first();
            if (!$commande) {
                // creer la commande avec juste l'acheteur_id et le statut en attente
                $commande = Commande::create([
                    'acheteur_id' => Auth::user()->acheteur->id,
                    'statut' => 'en_attente'
                ]);
            }
        }
        // creer la commande_produit associée
        $attributValeurs = ProduitAttributValeur::whereIn('id', $data['produit_attribut_valeurs'] ?? [])->get();
        $suplementCout = $attributValeurs->sum('supplement_cout');
        $data['prix_unitaire'] = Produit::findOrFail($data['produit_id'])->prix + $suplementCout;
        $commandeProduit = CommandeProduit::create([
            'commande_id' => $commande->id,
            'produit_id' => $data['produit_id'],
            'quantite' => $data['quantite'],
            'prix_unitaire' => $data['prix_unitaire'],
            'prix_total' => $data['quantite'] * $data['prix_unitaire']
        ]);
            
        // creer la commande_produit_attribut_valeur associée
        foreach ($attributValeurs as $attributValeur) {
            CommandeProduitAttributValeur::create([
                'commande_produit_id' => $commandeProduit->id,
                'produit_attribut_valeur_id' => $attributValeur->id
            ]);
        }

        // ajouter le produit au panier
        $panierItem = Auth::user()->acheteur->paniers()->updateOrCreate(
            [
                'commande_produit_id' => $commandeProduit->id,
                'produit_id' => $data['produit_id']
            ]
        );

        $panierItem->quantite = $data['quantite'];
        $panierItem->prix_unitaire = $data['prix_unitaire'];
        $panierItem->prix_total = $data['quantite'] * $data['prix_unitaire'];

        DB::commit();
        return response()->json([
            'message' => 'Produit ajouté au panier', 
            'item' => new PanierResource($panierItem)
        ]);
    }

    // Not used Method. To be deleted later
    public function update(Request $request, $id)
    {
        $panierItem = Panier::findOrFail($id);
        $this->authorize('update', $panierItem);

        $data = $request->validate([
            'quantite' => 'required|integer|min:1'
        ]);

        $panierItem->update(['quantite' => $data['quantite']]);

        return response()->json(['message' => 'Panier mis à jour', 'item' => $panierItem]);
    }

    public function destroy($id)
    {
        $panier = Auth::user()->acheteur->paniers()->get();
        if ($panier->count() === 1) {
            $commandeProduitId = $panier->first()->commande_produit_id;

            if ($id != $panier->first()->produit_id) {
                return response()->json(['message' => 'Produit non trouvé dans le panier'], 404);
            }

            CommandeProduit::where('id', $commandeProduitId)->delete();
            // La suppression en cascade est gérée par la base de données
            // CommandeProduitAttributValeur::where('commande_produit_id', $commandeProduitId)->delete();
            // $panier->first()->delete();
            Commande::where('acheteur_id', Auth::user()->acheteur->id)
                ->where('statut', 'en_attente')
                ->delete();
        }else {
            $panier = $panier->filter(function($item) use ($id) {
                return $item->produit_id == $id;
            })->first();

            if (!$panier) {
                return response()->json(['message' => 'Produit non trouvé dans le panier'], 404);
            }
            
            CommandeProduit::where('id', $panier->commande_produit_id)->delete();
            // La suppression en cascade est gérée par la base de données
            // CommandeProduitAttributValeur::where('commande_produit_id', $panier->commande_produit_id)->delete();
            // $panier->delete();
        }

        return response()->json(['message' => 'Produit retiré du panier']);
    }

    public function flushPanier()
    {
        $acheteur = Auth::user()->acheteur;
        $panierModel = new Panier();
        $panierModel->flushPanier($acheteur);

        // Supprimer la commande en attente associée
        Commande::where('acheteur_id', $acheteur->id)
            ->where('statut', 'en_attente')
            ->delete();

        return response()->json(['message' => 'Panier vidé avec succès']);
    }
}

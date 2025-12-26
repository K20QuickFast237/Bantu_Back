<?php

namespace App\Http\Controllers\Acheteur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Commande;
use App\Http\Resources\Commande\CommandeResource;
use App\Services\CommandeService;

class CommandeController extends Controller
{
    public function index()
    {
        // $commandes = Auth::user()->acheteur->commandes()->get();
        $commandes = Auth::user()->acheteur->commandes()->with('produits')
            ->where('statut', '!=', 'en_attente')->get();
        
        return CommandeResource::collection($commandes);
    }
    public function listAll()
    {
        $commandes = Commande::get();
        // $this->authorize('viewAll', $commandes);
        return CommandeResource::collection($commandes);
    }

    public function show($id)
    {
        // $commande = Commande::with(['produits', 'paiements'])->findOrFail($id);
        $commande = Commande::with(['acheteur', 'optionLivraison', 'modePaiement', 'produits', 'paiements', 'coupon'])->findOrFail($id);
        $this->authorize('view', $commande);
        return new CommandeResource($commande);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'option_livraison_id' => 'required|exists:mkt_option_livraisons,id',
            'mode_paiement_id' => 'required|exists:mkt_mode_paiements,id',
            'pays_livraison' => 'sometimes|nullable|string',
            'ville_livraison' => 'sometimes|nullable|string',
            'adresse_livraison' => 'sometimes|nullable|string',
            'coupon_code' => 'nullable|string'
        ]);

        $commandeService = app()->make(CommandeService::class);
        $commande = $commandeService->createCommande(Auth::user()->acheteur, $data);

        return new CommandeResource($commande);
    }
}

<?php

namespace App\Http\Controllers\Paiement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Paiement\PaiementResource;
use Illuminate\Support\Facades\Auth;
use App\Services\PaiementService;

class PaiementController extends Controller
{
    public function index()
    {
        $paiements = Auth::user()->acheteur->paiements()->with('commande')->get();
        return PaiementResource::collection($paiements);
    }

    public function initiate(Request $request)
    {
        $data = $request->validate([
            'commande_id' => 'required|exists:commandes,id',
            'mode_paiement_id' => 'required|exists:mode_paiements,id',
            'operateur_id' => 'required|exists:operateur_paiements,id',
        ]);

        $paiementService = app()->make(PaiementService::class);
        $paiement = $paiementService->initiatePaiement(Auth::user()->acheteur, $data);

        return new PaiementResource($paiement);
    }
}

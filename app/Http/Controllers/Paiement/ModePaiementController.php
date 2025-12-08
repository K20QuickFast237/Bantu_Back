<?php

namespace App\Http\Controllers\Paiement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Vendeur\ModePaiementResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ModePaiement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ModePaiementController extends Controller
{
    public function index()
    {
        $modesPaiement = ModePaiement::all();
        return response()->json($modesPaiement);
    }

    public function mesModes($id=null)
    {
        if ($id) {
            $user = User::whereHas('vendeur', function (Builder $query) use ($id) {
                $query->where('id', $id);
            })->first();
        }else{
            $user = Auth::user();
        }
        
        try {
            $modePaiement = $user->vendeur->modePaiements()->get();
        } catch (\Throwable $th) {
            return response()->json(["message" => "Vous n'avez pas les autorisations suffisantes."], 404);
        }
        return ModePaiementResource::collection($modePaiement);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isActive' => 'sometimes|boolean',
        ]);

        $data['nom'] = Str::ucfirst($data['nom']);

        $modePaiement = ModePaiement::create($data);
        return response()->json($modePaiement, 201);
    }

    public function addVendeurMode(Request $request){
        $data = $request->validate([
            'mode_id' => 'required|exists:mkt_mode_paiements,id'
        ]);
        $user = Auth::user();
        $vendeur = $user->vendeur;
        $vendeur->modePaiements()->attach($data['mode_id']);
        return response()->json([
            'message' => 'Mode de paiement ajouté.',
            'data' => new ModePaiementResource($vendeur->modePaiements->filter(function($mode) use ($data) {
                return $mode->id == $data['mode_id'];
            })->first())
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $modePaiement = ModePaiement::findOrFail($id);

        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'isActive' => 'sometimes|boolean',
        ]);

        if (isset($data['nom'])) {
            $data['nom'] = Str::ucfirst($data['nom']);
        }

        $modePaiement->update($data);
        return response()->json($modePaiement);
    }

    public function destroy($id)
    {
        try {
            $modePaiement = ModePaiement::findOrFail($id);
            // $this->authorize('delete', $modePaiement);
            $modePaiement->delete();
        } catch (\Throwable $th) {
        }
        return response()->json([
            "message" => "Mode de paiement supprimé."
        ]);
    }
}

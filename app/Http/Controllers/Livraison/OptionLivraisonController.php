<?php

namespace App\Http\Controllers\Livraison;

use App\Http\Controllers\Controller;
use App\Http\Resources\Vendeur\OptionLivraisonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\OptionLivraison;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OptionLivraisonController extends Controller
{
    public function index()
    {
        $optionLivraisons = OptionLivraison::all();
        return response()->json($optionLivraisons);
    }

    public function mesOptions($id=null)
    {
        if ($id) {
            $user = User::whereHas('vendeur', function (Builder $query) use ($id) {
                $query->where('id', $id);
            })->first();
        }else{
            $user = Auth::user();
        }
        
        try {
            $optionLivraisons = $user->vendeur->optionsLivraisons()->get();
        } catch (\Throwable $th) {
            return response()->json(["message" => "Vous n'avez pas les autorisations suffisantes."], 404);
        }
        return OptionLivraisonResource::collection($optionLivraisons);
    }

    public function addVendeurOption(Request $request){
        $data = $request->validate([
            'option_id' => 'required|exists:mkt_option_livraisons,id',
            'prix' => 'required|numeric|min:0',
        ]);
        $user = Auth::user();
        $vendeur = $user->vendeur;
        $vendeur->optionsLivraisons()->attach($data['option_id'], ['prix' => $data['prix']]);
        return response()->json([
            'message' => 'Option de livraison ajoutée.',
            'data' => new OptionLivraisonResource($vendeur->optionsLivraisons->filter(function($option) use ($data) {
                return $option->id == $data['option_id'];
            })->first())
        ], 201);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isActive' => 'sometimes|boolean',
        ]);

        $data['nom'] = Str::ucfirst($data['nom']);

        $optionLivraison = OptionLivraison::create($data);
        return response()->json([
            'message' => 'Option de livraison ajoutée.',
            'data' => $optionLivraison
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $optionLivraison = OptionLivraison::findOrFail($id);

        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'isActive' => 'sometimes|boolean',
        ]);

        if (isset($data['nom'])) {
            $data['nom'] = Str::ucfirst($data['nom']);
        }

        $optionLivraison->update($data);
        return response()->json($optionLivraison);
    }

    public function destroy($id)
    {
        try {
            $optionLivraison = OptionLivraison::findOrFail($id);
            // $this->authorize('delete', $optionLivraison);
            $optionLivraison->delete();
        } catch (\Throwable $th) {}

        return response()->json([
            'message' => 'Option de livraison supprimée.'
        ]);
    }

    public function delVendeurOption($id){
        // $this->authorize('delete', $vendeurOption);
        try {
            $vendeur = Auth::user()->vendeur;
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }
        $vendeur->optionsLivraisons()->where('option_id', $id)->detach(); // ->detach($id);
        return response()->json([
            'message' => 'Option de livraison supprimée de vos options.',
            'data' => $vendeur
        ]);
    }
}

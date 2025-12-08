<?php

namespace App\Http\Controllers\Produit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produit;
use App\Http\Resources\Produit\ProduitResource;
use App\Traits\UploadTrait;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    use UploadTrait;

    public function index()
    {
        $produits = Produit::with(['vendeur', 'categorie', 'medias', 'attributValeurs'])
            ->get();
            // ->paginate(12);
        return ProduitResource::collection($produits);
    }

    public function prodcat($categorie_id)
    {
        $produits = Produit::with(['vendeur', 'categorie', 'medias', 'attributValeurs'])
            ->where('categorie_id', $categorie_id)
            ->get();
            // ->paginate(12);
        return response()->json([
            'message' => 'Produits de la catégorie.',
            'produits' => ProduitResource::collection($produits)
        ]);
    }

    public function vendeurProduits()
    {
        try {
            $vendeurId = Auth::user()->vendeur->id;
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }
        $produits = Produit::with(['vendeur', 'categorie', 'medias', 'attributValeurs'])
            ->where('vendeur_id', $vendeurId)
            ->get();
        return ProduitResource::collection($produits);
    }

    public function show($id)
    {
        $produit = Produit::with(['vendeur', 'categorie', 'medias', 'attributValeurs'])->findOrFail($id);
        
        return new ProduitResource($produit);
    }

    public function store(Request $request)
    {
        if ($request->has('attribut_valeurs') && is_string($request->input('attribut_valeurs'))) {
            $decoded = json_decode($request->input('attribut_valeurs'), true);
            $request->merge(['attribut_valeurs' => $decoded]);
        }
        
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'stock_qtte' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:mkt_categorie_produits,id',
            'images.*' => 'nullable|image|max:2048',
            'videos.*' => 'nullable|file|max:10240',
            'documents.*' => 'nullable|file|max:10240',
            'attribut_valeurs' => 'nullable|array',
            'attribut_valeurs.*.id' => 'required_with:attribut_valeurs|integer|exists:mkt_attribut_valeurs,id',
            'attribut_valeurs.*.supplement_cout' => 'required_with:attribut_valeurs|numeric|min:0',
            'attribut_valeurs.*.stock_qtte' => 'required_with:attribut_valeurs|integer|min:0',
        ]);
        
        $produit = Produit::create([
            'vendeur_id' => Auth::user()->vendeur->id,
            'categorie_id' => $data['categorie_id'],
            'nom' => $data['nom'],
            'description' => $data['description'] ?? null,
            'prix' => $data['prix'],
            'stock_qtte' => $data['stock_qtte']
        ]);
        
        // Handle attribut_valeurs
        if (isset($data['attribut_valeurs'])) {
            foreach ($data['attribut_valeurs'] as $valeur) {
                $produit->attributValeurs()->attach($valeur['id'], [
                    'supplement_cout' => $valeur['supplement_cout'],
                    'stock_qtte' => $valeur['stock_qtte'],
                ]);
            }
        }

        // Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $produit->medias()->create([
                    'image_link' => $this->uploadFile($image, 'produits/images')
                ]);
            }
        }
        
        // Upload videos
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $produit->medias()->create([
                    'video_link' => $this->uploadFile($video, 'produits/videos')
                ]);
            }
        }

        // Upload documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $produit->medias()->create([
                    'document_link' => $this->uploadFile($doc, 'produits/documents')
                ]);
            }
        }

        return new ProduitResource($produit);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'prix' => 'sometimes|numeric|min:0',
            'stock_qtte' => 'sometimes|integer|min:0',
            'categorie_id' => 'sometimes|exists:categorie_produits,id'
        
        ]);
        
        $produit = Produit::findOrFail($id);

        // $this->authorize('update', $produit);
        try {
            $vendeurId = Auth::user()->vendeur->id;
            if ($produit->vendeur->id != $vendeurId) {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        $produit->update($data);

        return response()->json(['message' => 'Produit mis à jour avec succès', 'produit' => new ProduitResource($produit)]);
    }

    public function destroy($id)
    {
        $produit = Produit::findOrFail($id);
        // $this->authorize('delete', $produit);
        try {
            $vendeurId = Auth::user()->vendeur->id;
            if ($produit->vendeur->id != $vendeurId) {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        $produit->delete();
        return response()->json(['message' => 'Produit supprimé avec succès']);
    }

    public function search(Request $request)
    {
        $query = Produit::query();

        if ($request->has('nom')) {
            $query->where('nom', 'like', '%' . $request->input('nom') . '%');
        }

        if ($request->has('categorie')) {
            $query->where('categorie_id', $request->input('categorie_id'));
        }

        if ($request->has('prix_min')) {
            $query->where('prix', '>=', $request->input('prix_min'));
        }

        if ($request->has('prix_max')) {
            $query->where('prix', '<=', $request->input('prix_max'));
        }

        if ($request->has('vendeur')) {
            $query->where('vendeur_id', $request->input('vendeur'));
        }

        $perPage = $request->input('per_page');
        if ($perPage) {
            $produits = $query->paginate($perPage);
        }else {
            $produits = $query->get();
        }

        return ProduitResource::collection($produits);
    }

    public function addProduitAttributValeur(Request $request, int $id){
        $decoded = json_decode($request->input('attribut_valeurs'), true);
        $request->merge(['attribut_valeurs' => $decoded]);
        $data = $request->validate([
            'attribut_valeurs' => 'nullable|array',
            'attribut_valeurs.*.id' => 'required_with:attribut_valeurs|integer|exists:mkt_attribut_valeurs,id',
            'attribut_valeurs.*.supplement_cout' => 'required_with:attribut_valeurs|numeric|min:0',
            'attribut_valeurs.*.stock_qtte' => 'required_with:attribut_valeurs|integer|min:0',
        ]);
        $produit = Produit::findOrFail($id);
        // $this->authorize('delete', $produit);
        try {
            $vendeurId = Auth::user()->vendeur->id;
            if ($produit->vendeur->id != $vendeurId) {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        // Handle attribut_valeurs
        if (isset($data['attribut_valeurs'])) {
            foreach ($data['attribut_valeurs'] as $valeur) {
                $produit->attributValeurs()->attach($valeur['id'], [
                    'supplement_cout' => $valeur['supplement_cout'],
                    'stock_qtte' => $valeur['stock_qtte'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Attributs ajouté au produit.',
            'produit' => new ProduitResource($produit)
        ]); 
    }

    public function deleteProduitAttributValeur(int $produit_id, int $attribut_valeur_id){
        $produit = Produit::findOrFail($produit_id);
        // $this->authorize('delete', $produit);
        try {
            $vendeurId = Auth::user()->vendeur->id;
            if ($produit->vendeur->id != $vendeurId) {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        $produit->attributValeurs()->detach($attribut_valeur_id);

        return response()->json([
            'message' => 'Attribut supprimé du produit.',
            'produit' => new ProduitResource($produit)
        ]); 
    }

    public function addProduitMedia(Request $request, int $id){
        $data = $request->validate([
            'images.*' => 'nullable|image|max:2048',
            'videos.*' => 'nullable|file|max:10240',
            'documents.*' => 'nullable|file|max:10240'
        ]);
        $produit = Produit::findOrFail($id);
        // $this->authorize('delete', $produit);
        try {
            $vendeurId = Auth::user()->vendeur->id;
            if ($produit->vendeur->id != $vendeurId) {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        // Upload images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $produit->medias()->create([
                    'image_link' => $this->uploadFile($image, 'produits/images')
                ]);
            }
        }
        
        // Upload videos
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $produit->medias()->create([
                    'video_link' => $this->uploadFile($video, 'produits/videos')
                ]);
            }
        }

        // Upload documents
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                $produit->medias()->create([
                    'document_link' => $this->uploadFile($doc, 'produits/documents')
                ]);
            }
        }

        return response()->json([
            'message' => 'Média ajouté au produit.',
            'produit' => new ProduitResource($produit)
        ]);    
    }

    public function deleteProduitMedia(int $produit_id, int $media_id){
        $produit = Produit::findOrFail($produit_id);
        // $this->authorize('delete', $produit);
        try {
            $vendeurId = Auth::user()->vendeur->id;
            if ($produit->vendeur->id != $vendeurId) {
                throw new \Exception("Error Processing Request", 1);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Utilisateur non autorisé'], 403);
        }

        $produit->medias()->where('id', $media_id)->delete();

        return response()->json([
            'message' => 'Média supprimé du produit.',
            'produit' => new ProduitResource($produit)
        ]);
    }
}

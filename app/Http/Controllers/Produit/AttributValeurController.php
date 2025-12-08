<?php

namespace App\Http\Controllers\Produit;

use App\Http\Controllers\Controller;
use App\Models\AttributValeur;
use Illuminate\Http\Request;
use App\Http\Resources\Produit\AttributValeurResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AttributValeurController extends Controller
{
    /**
     * GET /api/attribut-valeurs
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        // $query = AttributValeur::with('attribut')->orderBy('id', 'desc');
        $query = AttributValeur::orderBy('id', 'desc');

        if ($q = $request->query('q')) {
            $query->where('nom', 'like', '%' . $q . '%');
        }

        // $paginated = $query->paginate($perPage);
        $paginated = $query->get();

        return response()->json(AttributValeurResource::collection($paginated)->response()->getData(true));
    }

    /**
     * POST /api/attribut-valeurs
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'attribut_id' => ['required', 'integer', 'exists:mkt_attributs,id'],
            'nom' => [
                'required', 'string', 'max:255',
                Rule::unique('mkt_attribut_valeurs', 'nom')->where(fn ($query) => $query->where('attribut_id', $request->input('attribut_id')))
            ],
        ]);

        $valeur = AttributValeur::create($data);

        return (new AttributValeurResource($valeur))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/attribut-valeurs/{id}
     */
    public function show($id): JsonResponse
    {
        $valeur = AttributValeur::with('attribut')->findOrFail($id);

        return (new AttributValeurResource($valeur))->response();
    }

    /**
     * PUT/PATCH /api/attribut-valeurs/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $valeur = AttributValeur::findOrFail($id);

        $data = $request->validate([
            'attribut_id' => ['sometimes', 'required', 'integer', 'exists:mkt_attributs,id'],
            'nom' => [
                'required', 'string', 'max:255',
                Rule::unique('mkt_attribut_valeurs', 'nom')
                    ->where(fn ($query) => $query->where('attribut_id', $request->input('attribut_id', $valeur->attribut_id)))
                    ->ignore($valeur->id),
            ],
        ]);

        $valeur->update($data);

        return (new AttributValeurResource($valeur))->response();
    }

    /**
     * DELETE /api/attribut-valeurs/{id}
     */
    public function destroy($id): JsonResponse
    {
        $valeur = AttributValeur::findOrFail($id);
        $valeur->delete();

        return response()->json("Deletion completed.", 200);
    }
}
<?php

namespace App\Http\Controllers\Produit;

use App\Http\Controllers\Controller;
use App\Models\Attribut;
use Illuminate\Http\Request;
use App\Http\Resources\Produit\AttributResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AttributController extends Controller
{
    /**
     * GET /api/attributs
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Attribut::with('valeurs')->orderBy('id', 'desc');

        // optional search by name
        if ($q = $request->query('q')) {
            $query->where('nom', 'like', '%' . $q . '%');
        }

        // $paginated = $query->paginate($perPage);
        $paginated = $query->get();

        return response()->json(AttributResource::collection($paginated)->response()->getData(true));
    }

    /**
     * POST /api/attributs
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('mkt_attributs', 'nom')],
        ]);

        $attribut = Attribut::create($data);

        return (new AttributResource($attribut->load('valeurs')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/attributs/{id}
     */
    public function show($id): JsonResponse
    {
        $attribut = Attribut::with('valeurs')->findOrFail($id);

        return (new AttributResource($attribut))->response();
    }

    /**
     * PUT/PATCH /api/attributs/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $attribut = Attribut::findOrFail($id);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', Rule::unique('mkt_attributs', 'nom')->ignore($attribut->id)],
        ]);

        $attribut->update($data);

        return (new AttributResource($attribut->load('valeurs')))->response();
    }

    /**
     * DELETE /api/attributs/{id}
     */
    public function destroy($id): JsonResponse
    {
        $attribut = Attribut::findOrFail($id);
        $attribut->delete();

        return response()->json("Deletion completed.", 200);
    }
}
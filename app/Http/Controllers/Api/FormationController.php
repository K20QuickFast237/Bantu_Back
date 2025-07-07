<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreFormationRequest;
use App\Models\Formation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FormationController extends Controller
{

    use AuthorizesRequests;

    public function index()
    {
        return response()->json(Formation::where('user_id', auth()->id())->get());
    }

    public function store(StoreFormationRequest $request)
    {
        $formation = Formation::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Formation ajoutée avec succès.', 'data' => $formation], 201);
    }

    public function update(StoreFormationRequest $request, Formation $formation)
    {
        $this->authorize('update', $formation);

        $formation->update($request->validated());

        return response()->json(['message' => 'Formation mise à jour.', 'data' => $formation]);
    }

    public function destroy(Formation $formation)
    {
        $this->authorize('delete', $formation);

        $formation->delete();

        return response()->json(['message' => 'Formation supprimée.']);
    }



}

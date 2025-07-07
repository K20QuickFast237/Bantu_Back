<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreExperienceRequest;
use App\Models\Experience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExperienceController extends Controller
{

    use AuthorizesRequests;

    public function index()
    {
        return response()->json(Experience::where('user_id', auth()->id())->get());
    }

    public function store(StoreExperienceRequest $request)
    {
        $experience = Experience::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Expérience ajoutée avec succès.', 'data' => $experience], 201);
    }

    public function update(StoreExperienceRequest $request, Experience $experience)
    {
        $this->authorize('update', $experience);

        $experience->update($request->validated());

        return response()->json(['message' => 'Expérience mise à jour.', 'data' => $experience]);
    }

    public function destroy(Experience $experience)
    {
        $this->authorize('delete', $experience);

        $experience->delete();

        return response()->json(['message' => 'Expérience supprimée.']);
    }
}

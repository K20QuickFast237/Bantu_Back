<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Enums\RoleValues;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreParticulierRequest;
use App\Http\Requests\Api\UpdateParticulierRequest;
use App\Models\Particulier;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class ParticulierProfileController extends Controller
{
    public function store(StoreParticulierRequest $request)
    {
        $user = Auth::guard('api')->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        if ($request->hasFile('image_profil')) {
            $data['image_profil'] = $request->file('image_profil')->store('images/profils', 'public');
        }

        $particulier = Particulier::create($data);

        if ($user->rolerole_actif !== RoleValues::RECRUTEUR) {
            $user->update(['role_actif' => RoleValues::CANDIDAT]);
        }

        return response()->json([
            'message' => "Profil complété avec succès",
            'data' => $particulier,
        ], 201);
    }

    public function update(UpdateParticulierRequest $request)
    {
        $user = Auth::guard('api')->user();
        $particulier = $user->particulier;

        if (!$particulier) {
            return response()->json(['message' => 'Profil non trouvé.'], 404);
        }

        $data = $request->validated();

        if ($request->hasFile('image_profil')) {
            $data['image_profil'] = $request->file('image_profil')->store('images/profils', 'public');
        }

        $particulier->update($data);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'data' => $particulier,
        ]);
    }

}

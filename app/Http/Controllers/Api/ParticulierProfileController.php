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

        if ($request->hasFile('image_profil_file')) {
            $data['image_profil'] = $request->file('image_profil_file')->store('images/profils', 'public');
        }
        if ($request->has('image_profil_link')) {
            $data['image_profil'] = $request->input('image_profil_link');
        }

        if ($request->hasFile('cv_file')) {
            $data['cv_link'] = $request->file('cv_file')->store('cvs', 'public');
        }
        if ($request->has('cv_link')) {
            $data['cv_link'] = $request->input('cv_link');
        }

        if ($request->hasFile('lettre_motivation_file')) {
            $data['lettre_motivation_link'] = $request->file('lettre_motivation_file')->store('motivations', 'public');
        }
        if ($request->has('lettre_motivation_link')) {
            $data['lettre_motivation_link'] = $request->input('lettre_motivation_link');
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

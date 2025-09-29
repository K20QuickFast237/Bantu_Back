<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Enums\RoleValues;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreParticulierRequest;
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
            'message' => "Profil ".RoleValues::CANDIDAT." complété avec succès",
            'data' => $particulier,
        ], 201);
    }

    
}

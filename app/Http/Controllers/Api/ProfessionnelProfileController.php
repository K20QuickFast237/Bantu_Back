<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Enums\RoleValues;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreProfessionnelRequest;
use App\Models\Professionnel;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class ProfessionnelProfileController extends Controller
{
    public function store(StoreProfessionnelRequest $request)
    {
        $user = Auth::guard('api')->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $professionnel = Professionnel::create($data);
        $user->update(['role_actif' => RoleValues::RECRUTEUR]);

        return response()->json([
            'message' => "Profil ".RoleValues::RECRUTEUR." complété avec succès",
            'data' => $professionnel,
        ], 201);
    }
}

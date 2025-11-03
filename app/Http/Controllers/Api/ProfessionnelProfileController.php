<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Enums\RoleValues;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreProfessionnelRequest;
use App\Http\Requests\Api\UpdateProfessionnelRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Professionnel;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfessionnelProfileController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreProfessionnelRequest $request)
    {
        $user = Auth::guard('api')->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('photo_couverture')) {
            $data['photo_couverture'] = $request->file('photo_couverture')->store('photos_couvertures', 'public');
        }

        $professionnel = Professionnel::create($data);
        $user->update(['role_actif' => RoleValues::RECRUTEUR]);

        return response()->json([
            'message' => "Profil complété avec succès",
            'data' => $professionnel,
        ], 201);
    }

    public function update(UpdateProfessionnelRequest $request, Professionnel $professionnel)
    {
        $this->authorize('update', $professionnel);

        $data = $request->validated();

        // dd($data);

        if ($request->hasFile('logo')) {
            if ($professionnel->logo && Storage::disk('public')->exists($professionnel->logo)) {
                Storage::disk('public')->delete($professionnel->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('photo_couverture')) {
            if ($professionnel->photo_couverture && Storage::disk('public')->exists($professionnel->photo_couverture)) {
                Storage::disk('public')->delete($professionnel->photo_couverture);
            }
            $data['photo_couverture'] = $request->file('photo_couverture')->store('photos_couvertures', 'public');
        }

        $professionnel->update($data);

        return response()->json([
            'message' => "Profil mis à jour avec succès",
            'data' => $professionnel->fresh(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Enums\RoleValues;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreProfessionnelRequest;
use App\Http\Requests\Api\StoreVendeurRequest;
use App\Http\Requests\Api\UpdateProfessionnelRequest;
use App\Http\Resources\ProfessionnelResource;
use App\Http\Resources\Vendeur\VendeurResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Professionnel;
use App\Models\Role;
use App\Models\Vendeur;
use Illuminate\Database\UniqueConstraintViolationException;
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

        try {
            $professionnel = Professionnel::create($data);
        } catch (UniqueConstraintViolationException $th) {
            return response()->json(['message' => "Un profil professionnel existe déjà pour cet utilisateur.",], 409);
        }

        $user->update(['role_actif' => RoleValues::RECRUTEUR]);

        return response()->json([
            'message' => "Profil complété avec succès",
            'data' => new ProfessionnelResource($professionnel),
        ], 201);
    }

    public function registerVendeur(StoreVendeurRequest $request)
    {
        $user = Auth::user();
        
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['statut'] = 'actif';

        if ($request->hasFile('logo')) {
            $data['logo_img'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('couverture')) {
            $data['couverture_img'] = $request->file('photo_couverture')->store('photos_couvertures', 'public');
        }

        try {
            $vendeur = Vendeur::create($data);
        } catch (UniqueConstraintViolationException $th) {
            return response()->json(['message' => "Un profil professionnel existe déjà pour cet utilisateur.",], 409);
        }

        $user->roles()->attach(Role::where('name', 'Vendeur')->first());
        $user->update(['role_actif' => RoleValues::VENDEUR]);

        return response()->json([
            'message' => "Profil complété avec succès",
            'data' => new VendeurResource($vendeur),
        ], 201);
    }

    public function update(UpdateProfessionnelRequest $request, Professionnel $professionnel)
    {
        $this->authorize('update', $professionnel);

        $data = $request->validated();

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

    public function registerFreelancer(Request $request)
    {
        // Implementation for registering a freelancer profile
    }
}

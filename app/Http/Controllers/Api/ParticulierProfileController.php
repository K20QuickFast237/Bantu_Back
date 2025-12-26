<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Enums\RoleValues;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreParticulierRequest;
use App\Http\Requests\Api\UpdateParticulierRequest;
use App\Http\Resources\Acheteur\AcheteurResource;
use App\Http\Resources\ParticulierResource;
use App\Http\Resources\Vendeur\VendeurResource;
use App\Models\Particulier;
use App\Models\User;
use App\Models\Acheteur;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Database\UniqueConstraintViolationException;

class ParticulierProfileController extends Controller
{
    public function store(StoreParticulierRequest $request)
    {
        $user = Auth::user(); // Auth::guard('api')->user();

        $data = $request->validated();
        $data['user_id'] = $user->id;
        $this->extractParticulierData($request, $data);

        try {
            $particulier = Particulier::create($data);
        } catch (UniqueConstraintViolationException $th) {
            return response()->json([
                'message' => 'Un profil particulier existe déjà pour cet utilisateur.'
            ], 409);
        }

        if ($user->rolerole_actif !== RoleValues::RECRUTEUR) {
            $user->update(['role_actif' => RoleValues::CANDIDAT]);
        }

        return response()->json([
            'message' => "Profil complété avec succès",
            'data' => new ParticulierResource($particulier),
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

        $this->extractParticulierData($request, $data);

        $particulier->update($data);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'data' => new ParticulierResource($particulier),
        ]);
    }

    private function extractParticulierData(Request $request, array &$data)
    {
        if ($request->hasFile('image_profil_file')) {
            $data['image_profil'] = $request->file('image_profil_file')->store('images/profils', 'public');
            unset($data['image_profil_file']);
        }
        if ($request->has('image_profil_link')) {
            $data['image_profil'] = $request->input('image_profil_link');
            unset($data['image_profil_link']);
        }

        if ($request->hasFile('cv_file')) {
            $data['cv_link'] = $request->file('cv_file')->store('cvs', 'public');
            unset($data['cv_file']);
        }
        if ($request->has('cv_link')) {
            $data['cv_link'] = $request->input('cv_link');
        }

        if ($request->hasFile('lettre_motivation_file')) {
            $data['lettre_motivation_link'] = $request->file('lettre_motivation_file')->store('motivations', 'public');
            unset($data['lettre_motivation_file']);
        }
        if ($request->has('lettre_motivation_link')) {
            $data['lettre_motivation_link'] = $request->input('lettre_motivation_link');
        }
    }

    public function registerAcheteur(Request $request)
    {
        // $data = $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|string|min:6|confirmed',
        //     'phone' => 'nullable|string|max:20'
        // ]);

        $user = Auth::user();
        $user->roles()->attach(Role::where('name', 'Acheteur')->first()); // assignRole('acheteur');

        $acheteur = Acheteur::create([
            'user_id' => $user->id,
            'infos_livraison' => [],
            'infos_paiement' => []
        ]);

        if ($user->rolerole_actif !== RoleValues::VENDEUR) {
            $user->update(['role_actif' => RoleValues::ACHETEUR]);
        }

        return response()->json([
            'message' => "Profil complété avec succès",
            'data' => new AcheteurResource($acheteur),
        ], 201);
    }
}

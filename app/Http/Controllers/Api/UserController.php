<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\userRole;
use App\Http\Resources\UserSkillResource;
use App\Models\Role;
use App\Models\Skill;
use Illuminate\Database\UniqueConstraintViolationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json(new UserResource($user));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUserSkills(User $user): JsonResponse
    {
        $skills = UserSkillResource::collection($user->skills); // Récupère les compétences associées à l'utilisateur
        return response()->json(
            !$skills->isEmpty() ? $skills : ["message" => "Aucune compétence trouvée."]
        // return
        );
    }

    public function setUserSkill(Request $request, User $user): JsonResponse
    {
        $skill = $request->validate([
            'id' => 'required|exists:skills',
            'niveau' => 'nullable|string',
        ]);
        try {
            $user->skills()->attach($skill['id'], ['niveau' => isset($skill['niveau']) ? $skill['niveau'] : "Non défini"]); // Ajoute le rôle au utilisateur et met à jour l'attribut isCurrent si fourniid, ['isCurrent' => true]);
        } catch (UniqueConstraintViolationException $th) {
            $message = "Cet utilisateur pocède déjà cette compétence.";
        }
        return response()->json([
            "message" => $message ?? "Compétence ajoutée avec succès.",
        ]);
    }

    public function deleteUserSkill(User $user, Skill $skill): JsonResponse
    {
        $user->skills()->detach($skill->id);
        return response()->json([
            "message" => "Compétence retirée avec succès.",
        ]);
    }

    public function getUserRoles(User $user): JsonResponse
    {
        $roles = new userRole($user->roles);
        return response()->json(
            !$roles->isEmpty() ? $roles : ["message" => "Aucun rôle trouvé"]
        );
    }

    public function setUserRole(Request $request, User $user): JsonResponse
    {
        $role = $request->validate([
            'id' => 'required|exists:roles',
            'isCurrent' => 'nullable|boolean',
        ]);
        try {
            $user->roles()->attach($role['id'], ['isCurrent' => $role['isCurrent'] ?? false]); // Ajoute le rôle au utilisateur et met à jour l'attribut isCurrent si fourniid, ['isCurrent' => true]);
        }  catch (UniqueConstraintViolationException $th) {
            $message = "Cet utilisateur pocède déjà ce rôle.";
        }
        return response()->json([
            "message" => $message ?? "Rôle ajouté avec succès.",
        ]);
    }

    public function deleteUserRole(User $user, Role $role): JsonResponse
    {
        $user->roles()->detach($role->id);
        return response()->json([
            "message" => "Rôle retiré avec succès.",
        ]);
    }
}

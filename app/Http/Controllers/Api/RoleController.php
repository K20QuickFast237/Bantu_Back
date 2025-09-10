<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRoleRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Admin: Affiche tous les roles
     */
    public function index()
    {
        $roles = Role::all() ?? ["message" => "Aucun rôle trouvé."];
        return response()->json($roles);
    }

    /**
     * Admin: Enregistre un nouveau role
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());
        return response()->json($role, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return response()->json($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $role->update($request->validated());
        return response()->json(['message' => 'Role mis à jour.', 'data' => $role]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role supprimé.', 'data' => $role], 204);
    }

    public function getRoleUsers(Role $role): JsonResponse
    {
        $users = $role->users ?? ["message" => "Aucun utilisateur trouvé."]; // Récupère les utilisateurs associés au rôle
        return response()->json($users);
    }

    public function getUserRoles(User $user): JsonResponse
    {
        $roles = $user->roles ?? ["message" => "Aucun rôle trouvé."]; // Récupère les rôles associés à l'utilisateur
        return response()->json($roles);
    }
}

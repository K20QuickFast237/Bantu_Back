<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\StoreSkillRequest;
use App\Models\Skill;


class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Skill::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request)
    {
        $skill = Skill::create($request->validated());
        return response()->json($skill, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        return $skill;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSkillRequest $request, Skill $skill)
    {
        $skill->update($request->validated());
        return response()->json($skill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json(null, 204);
    }

    public function getSkillUsers(Skill $skill)
    {
        $users = $skill->users ?? ["message" => "Aucun utilisateur trouvé."]; // Récupère les utilisateurs associés à la compétence
        return response()->json($users);
    }
}
